<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Request as RequestModel;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;

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
                return $query->where('status', $status);
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
                return $query->where('status', $status);
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
        if (auth()->user()->role !== 'purok_leader' || 
            $requestModel->purok_id !== auth()->user()->purok_id) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);
        
        $requestModel->update([
            'status' => $validated['status'],
            'processed_at' => now(),
            'processed_by' => auth()->id()
        ]);

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
        $validated = $request->validate([
            'form_type' => 'required|string|max:255|in:barangay_clearance,business_permit,certificate_of_residency,certificate_of_indigency,other',
            'purpose' => 'required|string|max:255',
            'other_purpose' => 'required_if:form_type,other|string|max:255',
            'front_id_photo_data' => 'required|string',
            'back_id_photo_data' => 'required|string',
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
                $path = 'storage/ids/' . $filename;
                
                // Ensure the directory exists
                if (!file_exists(public_path('storage/ids'))) {
                    mkdir(public_path('storage/ids'), 0755, true);
                }
                
                // Save the image
                file_put_contents(public_path($path), $decodedImage);
                return $path;
            }
            return null;
        }

        try {
            // Save front ID photo
            $frontIdPath = saveIdPhoto($request->front_id_photo_data, $user, 'front');
            
            // Save back ID photo
            $backIdPath = saveIdPhoto($request->back_id_photo_data, $user, 'back');
            
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
            ];

            // Create the request
            $newRequest = RequestModel::create($requestData);

            return redirect()->route('requests.show', $newRequest->id)
                ->with('success', 'Request submitted successfully!');
                
        } catch (\Exception $e) {
            // Clean up any uploaded files if there was an error
            if (isset($frontIdPath) && file_exists(public_path($frontIdPath))) {
                unlink(public_path($frontIdPath));
            }
            if (isset($backIdPath) && file_exists(public_path($backIdPath))) {
                unlink(public_path($backIdPath));
            }
            
            return back()->with('error', 'Error processing your request: ' . $e->getMessage())
                         ->withInput();
        }
    }

    // Show a single request
    public function show(RequestModel $request)
    {
        $this->authorize('view', $request);
        
        // Load relationships for the view
        $request->load(['purok', 'purokApprover', 'barangayApprover']);
        
        // Calculate age from birth date if available
        if ($request->birth_date) {
            $request->age = now()->diffInYears($request->birth_date);
        }
        
        return view('requests.show', [
            'request' => $request,
            'puroks' => \App\Models\Purok::all(),
        ]);
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
            'form_type' => 'required|string|max:255|in:barangay_clearance,barangay_id,business_permit,certificate_of_residency,certificate_of_indigency,other',
            'purpose' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'purok_id' => 'sometimes|exists:puroks,id',
        ]);
        
        // Get the authenticated user
        $user = auth()->user();
        
        // Update only the request-specific fields
        $request->update([
            'form_type' => $validated['form_type'],
            'purpose' => $validated['purpose'],
            'remarks' => $validated['remarks'],
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

        return redirect()->route('requests.pending-purok')
            ->with('success', 'Purok clearance approved. The resident can now proceed to the barangay office.');
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
        $this->authorize('reject', $request);

        $data = $httpRequest->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $request->update([
            'status' => 'rejected',
            'barangay_notes' => $data['rejection_reason'],
        ]);

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

    public function pendingBarangay()
    {
        $this->authorize('viewPendingBarangay', RequestModel::class);
        
        $requests = RequestModel::where('status', 'purok_approved')
            ->with(['user', 'purok', 'purokApprover'])
            ->latest()
            ->paginate(15);

        return view('requests.pending-barangay', compact('requests'));
    }

    // Delete a request
    public function destroy(RequestModel $request)
    {
        $this->authorize('delete', $request);

        $request->delete();

        return redirect()->route('requests.index')->with('success', 'Request deleted.');
    }
}
