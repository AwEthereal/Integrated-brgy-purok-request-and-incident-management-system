<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Events\NewRequestCreated;
use App\Models\Request as RequestModel;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Mail\PurokClearanceApproved;
use App\Mail\PurokClearanceRejected;
use App\Mail\PurokClearanceStatusUpdate;

class RequestController extends Controller
{
    // List all requests for the logged-in user
    public function index()
    {
        $requests = Auth::user()
            ->requests()
            ->with('purok')
            ->when(request('form_type'), function($query, $formType) {
                return $query->where('form_type', $formType);
            })
            ->when(request('status'), function($query, $status) {
                // Handle all possible status values
                if ($status === 'approved') {
                    return $query->where('status', 'purok_approved');
                } else {
                    return $query->where('status', $status);
                }
            })
            ->latest()
            ->paginate(10)
            ->appends(request()->query());
            
        return view('requests.my-requests', compact('requests'));
    }
    
    /**
     * Display the authenticated user's requests
     *
     * @return \Illuminate\View\View
     */
    public function myRequests()
    {
        $requests = Auth::user()
            ->requests()
            ->with('purok')
            ->when(request('form_type'), function($query, $formType) {
                return $query->where('form_type', $formType);
            })
            ->when(request('status'), function($query, $status) {
                // Handle all possible status values
                if ($status === 'approved') {
                    return $query->where('status', 'purok_approved');
                } else {
                    return $query->where('status', $status);
                }
            })
            ->latest()
            ->paginate(10)
            ->appends(request()->query());
            
        return view('requests.my-requests', compact('requests'));
    }

    /**
     * Update the status of a request (approve/reject)
     *
     * @param  \App\Models\Request  $requestModel
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(RequestModel $requestModel, HttpRequest $request)
    {
        // Verify the user is authorized to update this request
        $user = auth()->user();
        if (($user->role !== 'purok_leader') || 
            $requestModel->purok_id !== $user->purok_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);
        
        if (strtolower($validated['status']) === 'approved') {
            $requestModel->status = 'purok_approved';
            $requestModel->purok_approved_at = now();
            $requestModel->purok_approved_by = $user->id;
            $requestModel->save();
            
            // Notify linked user if exists
            if ($requestModel->user) {
                $requestModel->user->notify(new \App\Notifications\RequestApprovedNotification($requestModel, 'purok'));
            }
            
            // Broadcast to barangay officials about new pending request
            $barangayRequestCount = RequestModel::where('status', 'purok_approved')->count();
            event(new \App\Events\NewBarangayRequest($requestModel, $barangayRequestCount));
        } else {
            $requestModel->status = 'rejected';
            $requestModel->purok_notes = 'Request rejected by purok leader';
            $requestModel->rejected_at = now();
            $requestModel->rejected_by = $user->id;
            $requestModel->save();
            
            // Notify linked user if exists
            if ($requestModel->user) {
                $requestModel->user->notify(new \App\Notifications\RequestRejectedNotification($requestModel, 'purok'));
            }
        }

        return back()->with('success', 'Request has been ' . $validated['status'] . ' successfully.');
    }

    // Show form to create a new request
    public function create()
    {
        return view('requests.create');
    }

    // Save a new request
    public function store(HttpRequest $request)
    {
        try {
            // Check if user has reached the pending request limit
            $user = auth()->user();
            $pendingCount = RequestModel::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'purok_approved'])
                ->count();
            
            if ($pendingCount >= 5) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have reached the maximum limit of 5 pending requests. Please wait for your existing requests to be processed before submitting a new one.'
                    ], 429);
                }
                
                return back()->withErrors([
                    'limit' => 'You have reached the maximum limit of 5 pending requests. Please wait for your existing requests to be processed before submitting a new one.'
                ])->withInput();
            }
            
            $validated = $request->validate([
                'form_type' => 'required|string|in:barangay_clearance,business_clearance,certificate_of_residency,certificate_of_indigency,other',
                'purpose' => 'required|string|max:50',
                'remarks' => 'nullable|string|max:100',
                'other_purpose' => 'required_if:form_type,other|string|max:255',
                'front_id_photo_data' => 'required|string',
                'back_id_photo_data' => 'required|string',
            ], [
                'purpose.max' => 'The purpose must not exceed 50 characters.',
                'remarks.max' => 'Additional notes must not exceed 100 characters.',
                'front_id_photo_data.required' => 'Please upload a photo of the front of your ID.',
                'back_id_photo_data.required' => 'Please upload a photo of the back of your ID.',
            ]);

            // Get the authenticated user
            $user = auth()->user();

            // Function to save ID photo
            function saveIdPhoto($imageData, $user, $suffix = 'front') {
                if (!$imageData) return null;
                
                // Check if the image data is a base64 string
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $imageType = strtolower($matches[1]);
                    
                    // Validate image type
                    if (!in_array($imageType, ['jpeg', 'jpg', 'png'])) {
                        throw new \Exception('Invalid image format. Only JPG and PNG are allowed.');
                    }
                    
                    // Decode the base64 data
                    $decodedImage = base64_decode($imageData);
                    
                    // Generate a unique filename
                    $filename = 'id_' . $user->id . '_' . $suffix . '_' . time() . '.' . $imageType;
                    
                    // Create the directory if it doesn't exist
                    if (!file_exists(public_path('storage/ids'))) {
                        mkdir(public_path('storage/ids'), 0755, true);
                    }
                    
                    // Use Laravel's storage system
                    $path = 'ids/' . $filename;
                    \Storage::disk('public')->put($path, $decodedImage);
                    
                    // Return the relative path without 'public/'
                    return 'storage/' . $path;
                }
                return null;
            }

            // Save front ID photo
            $frontIdPath = saveIdPhoto($request->front_id_photo_data, $user, 'front');
            if (!$frontIdPath) {
                throw new \Exception('Failed to save front ID photo. Please try again.');
            }
            
            // Save back ID photo
            $backIdPath = saveIdPhoto($request->back_id_photo_data, $user, 'back');
            if (!$backIdPath) {
                // Clean up front photo if back photo fails
                if (file_exists(public_path($frontIdPath))) {
                    unlink(public_path($frontIdPath));
                }
                throw new \Exception('Failed to save back ID photo. Please try again.');
            }
            
            // Prepare request data with user's information
            $requestData = [
                'user_id' => $user->id,
                'form_type' => $validated['form_type'],
                'purpose' => $validated['purpose'] . ($request->other_purpose ? ': ' . $request->other_purpose : ''),
                'contact_number' => $user->contact_number,
                'email' => $user->email,
                'birth_date' => $user->birth_date,
                'gender' => $user->gender,
                'civil_status' => $user->civil_status,
                'occupation' => $user->occupation,
                'address' => $user->address,
                'purok_id' => $user->purok_id,
                'status' => 'pending',
                'valid_id_front_path' => $frontIdPath,
                'valid_id_back_path' => $backIdPath,
                'remarks' => $validated['remarks'] ?? null,
            ];

            // Create the request
            $newRequest = RequestModel::create($requestData);
            
            // Get the count of pending requests for this purok
            $pendingCount = RequestModel::where('purok_id', $user->purok_id)
                ->where('status', 'pending')
                ->count();
            
            // Broadcast the event
            event(new NewRequestCreated($user->purok_id, $pendingCount));
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your request has been submitted successfully!',
                    'redirect' => route('dashboard')
                ]);
            }
            
            return redirect()->route('dashboard')
                ->with('success', 'Your request has been submitted successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Clean up any uploaded files if there was a validation error
            if (isset($frontIdPath) && file_exists(public_path($frontIdPath))) {
                unlink(public_path($frontIdPath));
            }
            if (isset($backIdPath) && file_exists(public_path($backIdPath))) {
                unlink(public_path($backIdPath));
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
                
        } catch (\Exception $e) {
            // Clean up any uploaded files if there was an error
            if (isset($frontIdPath) && file_exists(public_path($frontIdPath))) {
                unlink(public_path($frontIdPath));
            }
            if (isset($backIdPath) && file_exists(public_path($backIdPath))) {
                unlink(public_path($backIdPath));
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error processing your request: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Error processing your request: ' . $e->getMessage())
                         ->withInput();
        }
    }

    // Show a single request
    public function show(RequestModel $request)
    {
        try {
            // Check if the user is authorized to view this request
            if (!auth()->user()->can('view', $request)) {
                $errorMessage = 'You are not authorized to view this request.';
                
                // Provide more specific error message for purok leaders
                if ((auth()->user()->role === 'purok_leader') && 
                    $request->purok_id !== auth()->user()->purok_id) {
                    $errorMessage = 'You can only view requests from your own purok.';
                }
                
                abort(403, $errorMessage);
            }
            
            // Mark as viewed by the resident (owner) when they open the request
            if (auth()->user()->id === $request->user_id) {
                $request->timestamps = false; // Prevent updated_at from being modified
                $request->last_viewed_at = now();
                $request->save();
                $request->timestamps = true; // Re-enable timestamps for future operations
            }
            
            // Load relationships for the view
            $request->load(['purok', 'purokApprover', 'barangayApprover']);
            
            // Calculate age from birth date if available
            if ($request->birth_date) {
                $request->age = now()->diffInYears($request->birth_date);
            }
            
            // Ensure the file paths are correct and accessible
            if ($request->valid_id_front_path) {
                // If the path is already a URL, use it as is
                if (str_starts_with($request->valid_id_front_path, 'http')) {
                    // Do nothing, path is already a full URL
                } 
                // Check if the file exists in the public storage
                $frontFilename = basename(str_replace('storage/ids/', '', $request->valid_id_front_path));
                if (Storage::disk('public')->exists('ids/' . $frontFilename)) {
                    $request->valid_id_front_path = asset('storage/ids/' . $frontFilename);
                }
                // If the file exists in the private storage, generate a temporary URL
                elseif (Storage::disk('local')->exists($request->valid_id_front_path)) {
                    $request->valid_id_front_path = Storage::disk('local')->temporaryUrl(
                        $request->valid_id_front_path, 
                        now()->addMinutes(5)
                    );
                }
            }
            
            if ($request->valid_id_back_path) {
                // If the path is already a URL, use it as is
                if (str_starts_with($request->valid_id_back_path, 'http')) {
                    // Do nothing, path is already a full URL
                } 
                // Check if the file exists in the public storage
                $backFilename = basename(str_replace('storage/ids/', '', $request->valid_id_back_path));
                if (Storage::disk('public')->exists('ids/' . $backFilename)) {
                    $request->valid_id_back_path = asset('storage/ids/' . $backFilename);
                }
                // If the file exists in the private storage, generate a temporary URL
                elseif (Storage::disk('local')->exists($request->valid_id_back_path)) {
                    $request->valid_id_back_path = Storage::disk('local')->temporaryUrl(
                        $request->valid_id_back_path, 
                        now()->addMinutes(5)
                    );
                }
            }
            
            return view('requests.show', [
                'request' => $request,
                'puroks' => \App\Models\Purok::all(),
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error showing request: ' . $e->getMessage());
            
            // Return a user-friendly error page
            return back()->with('error', 'An error occurred while trying to view this request. Please try again later.');
        }
    }

    // Show form to edit a request
    public function edit(RequestModel $request)
    {
        $this->authorize('update', $request);
        
        // Only allow editing if request is still pending
        if ($request->status !== 'pending') {
            return redirect()->route('requests.show', $request)
                ->with('error', 'You can only edit requests that are still pending.');
        }
        
        // Eager load the user relationship to prevent N+1 queries
        $request->load('user');
        
        return view('requests.edit', [
            'request' => $request,
            'puroks' => \App\Models\Purok::all(),
        ]);
    }

    // Update a request
    public function update(HttpRequest $httpRequest, RequestModel $request)
    {
        $this->authorize('update', $request);
        
        // Only allow updating if request is still pending
        if ($request->status !== 'pending') {
            return redirect()->route('requests.show', $request)
                ->with('error', 'You can only edit requests that are still pending.');
        }

        $validated = $httpRequest->validate([
            // Request details only
            'form_type' => 'required|string|max:255|in:barangay_clearance,barangay_id,business_clearance,certificate_of_residency,certificate_of_indigency,other',
            'purpose' => 'required|string|max:50',
            'remarks' => 'nullable|string|max:100',
            'purok_id' => 'sometimes|exists:puroks,id',
        ], [
            'purpose.max' => 'The purpose must not exceed 50 characters.',
            'remarks.max' => 'Additional notes must not exceed 100 characters.',
        ]);
        
        // Get the authenticated user
        $user = auth()->user();
        
        // Update only the request-specific fields
        $request->update([
            'form_type' => $validated['form_type'],
            'purpose' => $validated['purpose'],
            'remarks' => $validated['remarks'] ?? null,
            'purok_id' => $validated['purok_id'] ?? $request->purok_id,
            // User's profile data is automatically used from the user model
            'contact_number' => $user->contact_number,
            'email' => $user->email,
            'birth_date' => $user->birth_date,
            'gender' => $user->gender,
            'civil_status' => $user->civil_status,
            'occupation' => $user->occupation,
            'address' => $user->address
        ]);

        return redirect()->route('requests.show', $request)
            ->with('success', 'Request updated successfully.');
    }

    // Approve request at purok level
    public function approvePurok(RequestModel $request, HttpRequest $httpRequest)
    {
        $this->authorize('approvePurok', $request);

        $data = $httpRequest->validate([
            'purok_notes' => 'nullable|string',
        ]);

        $request->update([
            'status' => 'purok_approved',
            'purok_approved_at' => now(),
            'purok_approved_by' => Auth::id(),
            'purok_notes' => $data['purok_notes'] ?? null,
        ]);

        // Determine recipient email (public or linked user)
        $recipientEmail = $request->email ?? optional($request->user)->email;

        // Notify linked user if exists
        if ($request->user) {
            $request->user->notify(new \App\Notifications\RequestApprovedNotification($request, 'purok'));
        }

        // Send email if available
        if ($recipientEmail) {
            try {
                Mail::to($recipientEmail)->send(new PurokClearanceApproved($request, auth()->user()->full_name));
            } catch (\Exception $e) {
                \Log::error('Failed to send approval email: ' . $e->getMessage());
            }
        }

        // If final_purok_approval is enabled, mark as completed and skip barangay step
        if (config('features.final_purok_approval')) {
            $request->update([
                'status' => 'completed',
                'document_generated_at' => now(),
            ]);

            // Broadcast completion to resident
            event(new \App\Events\ResidentRequestUpdated($request, 'completed'));

            if (in_array(auth()->user()->role, ['secretary', 'barangay_captain'], true)) {
                return redirect()->route('reports.purok-clearance')
                    ->with('success', 'Purok clearance approved and completed.');
            }

            if (in_array(auth()->user()->role, ['purok_leader', 'admin'])) {
                return redirect()->route('purok_leader.dashboard')
                    ->with('success', 'Purok clearance approved and completed.');
            }

            return redirect()->route('requests.show', $request)
                ->with('success', 'Purok clearance approved and completed.');
        }

        // Otherwise continue existing flow
        event(new \App\Events\ResidentRequestUpdated($request, 'purok_approved'));
        $barangayRequestCount = \App\Models\Request::where('status', 'purok_approved')->count();
        event(new \App\Events\NewBarangayRequest($request, $barangayRequestCount));

        if (in_array(auth()->user()->role, ['secretary', 'barangay_captain'], true)) {
            return redirect()->route('reports.purok-clearance')
                ->with('success', 'Purok clearance approved. The resident can now proceed to the barangay office.');
        }

        if (in_array(auth()->user()->role, ['purok_leader', 'admin'])) {
            return redirect()->route('requests.pending-purok')
                ->with('success', 'Purok clearance approved. The resident can now proceed to the barangay office.');
        }

        return redirect()->route('requests.show', $request)
            ->with('success', 'Purok clearance approved. The resident can now proceed to the barangay office.');
    }
    
    /**
     * Update private notes for a request (visible only to purok leaders and admins)
     *
     * @param  \App\Models\Request  $request
     * @param  \Illuminate\Http\Request  $httpRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePrivateNotes(RequestModel $request, HttpRequest $httpRequest): \Illuminate\Http\JsonResponse
    {
        $this->authorize('updatePrivateNotes', $request);

        $data = $httpRequest->validate([
            'purok_private_notes' => 'nullable|string',
        ]);

        $request->update([
            'purok_private_notes' => $data['purok_private_notes'] ?? null,
        ]);

        // Always return JSON response for AJAX requests
        return response()->json([
            'success' => true,
            'message' => 'Private notes updated successfully.',
            'notes' => $request->purok_private_notes
        ]);
    }

    public function approveBarangay(RequestModel $request, HttpRequest $httpRequest)
    {
        $this->authorize('approveBarangay', $request);

        $data = $httpRequest->validate([
            'barangay_notes' => 'nullable|string',
        ]);

        $request->update([
            'status' => 'barangay_approved',
            'barangay_approved_at' => now(),
            'barangay_approved_by' => Auth::id(),
            'barangay_notes' => $data['barangay_notes'] ?? null,
        ]);

        // Notify linked user if exists
        if ($request->user) {
            $request->user->notify(new \App\Notifications\RequestApprovedNotification($request, 'barangay'));
        }
        // Email to public or linked user
        $recipientEmail = $request->email ?? optional($request->user)->email;
        if ($recipientEmail) {
            try {
                Mail::to($recipientEmail)->send(new PurokClearanceApproved($request, auth()->user()->full_name));
            } catch (\Exception $e) {
                \Log::error('Failed to send approval email: ' . $e->getMessage());
            }
        }

        // Broadcast real-time notification to resident
        event(new \App\Events\ResidentRequestUpdated($request, 'barangay_approved'));

        return redirect()->route('requests.pending-barangay')
            ->with('success', 'Barangay clearance approved. Document is ready for release.');
    }

    public function complete(RequestModel $request)
    {
        $this->authorize('complete', $request);

        $request->update([
            'status' => 'completed',
            'document_generated_at' => now(),
        ]);

        return redirect()->route('requests.show', $request)
            ->with('success', 'Document marked as completed.');
    }

    public function reject(RequestModel $request, HttpRequest $httpRequest)
    {
        $user = auth()->user();
        
        // Use Laravel's authorization
        if (!auth()->user()->can('reject', $request)) {
            abort(403, 'You are not authorized to reject this request.');
        }

        $data = $httpRequest->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $updates = [
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => $user->id,
            'rejection_reason' => $data['rejection_reason']
        ];

        // Store rejection reason in appropriate field based on user role
        if ($user->role === 'purok_leader') {
            $updates['purok_notes'] = 'Rejected by Purok: ' . $data['rejection_reason'];
        } elseif (in_array($user->role, ['barangay_captain', 'barangay_kagawad', 'secretary', 'sk_chairman', 'admin'])) {
            $updates['barangay_notes'] = 'Rejected by Barangay: ' . $data['rejection_reason'];
        } else {
            $updates['barangay_notes'] = 'Rejected: ' . $data['rejection_reason'];
        }

        // Update the request
        $request->update($updates);

        // Notify linked user if exists
        $approvalType = ($user->role === 'purok_leader') ? 'purok' : 'barangay';
        if ($request->user) {
            $request->user->notify(new \App\Notifications\RequestRejectedNotification($request, $approvalType));
        }
        // Email to public or linked user
        $recipientEmail = $request->email ?? optional($request->user)->email;
        if ($recipientEmail) {
            try {
                Mail::to($recipientEmail)->send(new PurokClearanceRejected($request, $user->full_name, $data['rejection_reason']));
            } catch (\Exception $e) {
                \Log::error('Failed to send rejection email: ' . $e->getMessage());
            }
        }

        // Broadcast real-time notification to resident
        event(new \App\Events\ResidentRequestUpdated($request, 'rejected', 'Your request has been rejected'));

        // Redirect based on user role
        if ($user->role === 'purok_leader') {
            return redirect()->route('purok_leader.dashboard')
                ->with('success', 'Request has been rejected.');
        }

        return redirect()->route('requests.show', $request)
            ->with('success', 'Request has been rejected.');
    }

    public function pendingPurok()
    {
        $this->authorize('viewPendingPurok', RequestModel::class);
        
        $requests = RequestModel::where('status', 'pending')
            ->with(['user', 'purok'])
            ->latest()
            ->paginate(15);

        return view('requests.pending-purok', compact('requests'));
    }

    public function pendingBarangay(HttpRequest $request)
    {
        $this->authorize('viewPendingBarangay', RequestModel::class);
        
        $status = $request->get('status', 'pending');
        
        $query = RequestModel::with(['user', 'purok', 'purokApprover']);
        
        // Filter by status
        if ($status === 'pending') {
            $query->where('status', 'purok_approved');
        } elseif ($status === 'completed') {
            $query->whereIn('status', ['barangay_approved', 'completed', 'rejected']);
        }
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Form type filter
        if ($request->filled('form_type')) {
            $query->where('form_type', $request->form_type);
        }
        
        // Purok filter
        if ($request->filled('purok')) {
            $query->where('purok_id', $request->purok);
        }
        
        $requests = $query->latest()->paginate(15)->appends($request->query());
        $pendingCount = RequestModel::where('status', 'purok_approved')->count();

        return view('requests.pending-barangay', compact('requests', 'pendingCount'));
    }

    // Delete a request
    public function destroy(RequestModel $request)
    {
        $this->authorize('delete', $request);

        // Only allow deletion if request is pending or rejected
        if (!in_array($request->status, ['pending', 'rejected'])) {
            return redirect()->route('requests.show', $request)
                ->with('error', 'You can only delete requests that are pending or rejected.');
        }

        // Delete associated ID photos if they exist
        if ($request->valid_id_front_path && file_exists(public_path($request->valid_id_front_path))) {
            unlink(public_path($request->valid_id_front_path));
        }
        if ($request->valid_id_back_path && file_exists(public_path($request->valid_id_back_path))) {
            unlink(public_path($request->valid_id_back_path));
        }

        $request->delete();

        return redirect()->route('requests.index')->with('success', 'Request deleted successfully.');
    }
}
