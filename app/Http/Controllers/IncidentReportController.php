<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncidentReport;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Purok;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IncidentReportController extends Controller
{

    private function safeRedirectTo(?string $redirectTo): ?string
    {
        if (!is_string($redirectTo) || trim($redirectTo) === '') {
            return null;
        }
        $redirectTo = trim($redirectTo);
        if (str_starts_with($redirectTo, '/')) {
            return $redirectTo;
        }
        if (str_starts_with($redirectTo, url('/'))) {
            return $redirectTo;
        }
        return null;
    }


    // Resident submits incident report
    public function store(Request $request)
    {
        $request->validate([
            'incident_type' => 'required|in:' . implode(',', array_keys(IncidentReport::TYPES)),
            'incident_type_other' => 'nullable|string|max:100',
            'description' => 'required|string',
            'photo_data' => 'nullable|string', // base64 string (first photo for backward compatibility)
            'photos_data' => 'nullable|string', // JSON array of base64 strings
            'photos' => 'nullable|array', // fallback file upload
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location' => 'nullable|string'
        ]);

        // Basic guard against extremely large base64 payloads that could exceed server limits.
        // (This is in bytes, not KB.)
        if ($request->filled('photo_data') && strlen((string) $request->input('photo_data')) > 15000000) {
            return back()->withErrors(['photo_data' => 'Photo payload is too large. Please upload a smaller photo.'])->withInput();
        }
        if ($request->filled('photos_data') && strlen((string) $request->input('photos_data')) > 45000000) {
            return back()->withErrors(['photos_data' => 'Photos payload is too large. Please upload smaller photos or fewer photos.'])->withInput();
        }

    $photoPath = null;
    $photoPaths = [];

    // Handle multiple photos from camera (photos_data)
    if ($request->filled('photos_data')) {
        try {
            $photosArray = json_decode($request->photos_data, true);
            if (is_array($photosArray)) {
                foreach ($photosArray as $index => $photoData) {
                    if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
                        $data = substr($photoData, strpos($photoData, ',') + 1);
                        $type = strtolower($type[1]);

                        if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                            continue;
                        }

                        $data = base64_decode($data);
                        if ($data === false) {
                            continue;
                        }

                        $fileName = 'incident_' . time() . '_' . $index . '.' . $type;
                        $path = 'incident_photos/' . $fileName;
                        Storage::disk('public')->put($path, $data);
                        $photoPaths[] = $path;
                        
                        // Set first photo as main photo for backward compatibility
                        if ($index === 0) {
                            $photoPath = $path;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing multiple photos: ' . $e->getMessage());
        }
    }
    
    // Fallback to single photo_data
    if (empty($photoPaths) && $request->filled('photo_data')) {
        $data = $request->photo_data;
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]);

            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                return back()->withErrors(['photo_data' => 'Invalid image type']);
            }

            $data = base64_decode($data);
            if ($data === false) {
                return back()->withErrors(['photo_data' => 'Base64 decode failed']);
            }

            $fileName = 'incident_' . time() . '.' . $type;
            $photoPath = 'incident_photos/' . $fileName;
            Storage::disk('public')->put($photoPath, $data);
            $photoPaths[] = $photoPath;
        }
    }
    
    // Handle file uploads
    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $index => $photo) {
            $path = $photo->store('incident_photos', 'public');
            $photoPaths[] = $path;
            
            if ($index === 0 && empty($photoPath)) {
                $photoPath = $path;
            }
        }
    }

    try {
        $incident = new IncidentReport();
        $incident->user_id = auth()->id();
        $incident->purok_id = auth()->user()->purok_id ?? null;
        $incident->incident_type = $request->incident_type;
        $incident->incident_type_other = $request->incident_type === 'other' ? ($request->incident_type_other ?: null) : null;
        $incident->description = $request->description;
        $incident->photo_path = $photoPath;
        $incident->photo_paths = !empty($photoPaths) ? $photoPaths : null;
        $incident->latitude = $request->latitude;
        $incident->longitude = $request->longitude;
        $incident->location = $request->location;
        $incident->status = IncidentReport::STATUS_PENDING;
        $incident->save();

        // Get the count of pending incidents for barangay officials
        $pendingCount = IncidentReport::whereIn('status', ['pending', 'in_progress'])->count();
        
        // Broadcast the event to barangay officials
        event(new \App\Events\NewIncidentReported($incident, $pendingCount));

        return redirect()->route('incident_reports.show', $incident->id)
            ->with('success', 'Incident report submitted successfully!');
    } catch (\Exception $e) {
        // Log the error
        Log::error('Error creating incident report: ' . $e->getMessage());
        
        // If there was an error, delete the uploaded photo if it exists
        if (isset($photoPath) && Storage::disk('public')->exists($photoPath)) {
            Storage::disk('public')->delete($photoPath);
        }
        
        return back()->withInput()
            ->withErrors(['error' => 'Failed to create incident report. Please try again.']);
    }
}


    /**
     * Show the form for creating a new incident report.
     *
     * @return \Illuminate\View\View
     */
    /**
     * Show the form for creating a new incident report.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $incidentTypes = IncidentReport::TYPES;
        $puroks = Purok::orderBy('name')->get();
        
        // Return the correct view path for creating incident reports
        return view('incidents.create', compact('incidentTypes', 'puroks'));
    }

    /**
     * Display a listing of the incident reports.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $reports = IncidentReport::with(['user', 'purok'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $pendingCount = IncidentReport::where('status', 'pending')->count();

        return view('admin.incidents.pending', compact('reports', 'pendingCount'));
    }
    
    /**
     * Display incident reports for barangay officials with filtering
     *
     * @return \Illuminate\View\View
     */
    public function pendingApproval(Request $request)
    {
        $status = $request->get('status', 'pending');
        
        $query = IncidentReport::with(['user', 'purok']);
        
        // Filter by status
        if ($status === 'pending') {
            $query->where('status', 'pending');
        } elseif ($status === 'completed') {
            // Apply specific status filter if provided
            $validStatuses = ['in_progress', 'resolved', 'rejected', 'invalid'];
            if ($request->has('incident_status') && in_array($request->query('incident_status'), $validStatuses)) {
                $query->where('status', $request->query('incident_status'));
            } else {
                // Include both 'resolved' and 'approved' for backward compatibility
                $query->whereIn('status', ['in_progress', 'resolved', 'approved', 'rejected', 'invalid']);
            }
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
        
        // Incident type filter
        if ($request->filled('incident_type')) {
            $query->where('incident_type', $request->incident_type);
        }
        
        // Purok filter
        if ($request->filled('purok')) {
            $query->where('purok_id', $request->purok);
        }
        
        $reports = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());
        $pendingCount = IncidentReport::where('status', 'pending')->count();

        return view('admin.incidents.pending', compact('reports', 'pendingCount'));
    }
    
    // Resident views their own incident reports
    public function myReports()
    {
        \Log::info('myReports method called', [
            'user_id' => auth()->id(),
            'url' => request()->fullUrl(),
            'ip' => request()->ip()
        ]);

        $reports = IncidentReport::where('user_id', auth()->id())
            ->when(request('type'), function($query, $type) {
                return $query->where('incident_type', $type);
            })
            ->when(request('status'), function($query, $status) {
                return $query->where('status', $status);
            })
            ->with('purok')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(request()->query());
            
        return view('incident_reports/my-reports', compact('reports'));
    }

    // Staff views a specific report
    public function show(Request $request, $id)
    {
        $report = IncidentReport::with(['user', 'purok'])->findOrFail($id);
        $redirectTo = $this->safeRedirectTo((string) $request->query('redirect_to', ''));
        
        // Check if the authenticated user is an admin or barangay official
        if (in_array(auth()->user()->role, ['admin', 'barangay_kagawad', 'barangay_captain', 'secretary', 'barangay_clerk', 'sk_chairman'])) {
            // Mark as viewed if not already viewed
            if (is_null($report->viewed_at)) {
                $report->update(['viewed_at' => now()]);
                // Refresh the report to get the updated viewed_at
                $report->refresh();
            }
            
            return view('admin.incidents.show', compact('report', 'redirectTo'));
        }
        
        // For residents, show the resident view
        return view('resident.incidents.show', compact('report', 'redirectTo'));
    }

    // Staff updates status or adds notes
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'staff_notes' => 'nullable|string',
        ]);

        $report = IncidentReport::findOrFail($id);
        $oldStatus = $report->status;
        
        $report->update([
            'status' => $request->status,
            'staff_notes' => $request->staff_notes,
        ]);

        // Send email notification if status changed
        if ($oldStatus !== $request->status) {
            if ($report->user) {
                $report->user->notify(new \App\Notifications\IncidentReportStatusNotification($report, $oldStatus, $request->status));
            }
        }

        return back()->with('success', 'Report updated and notification sent to resident.');
    }

    /**
     * Store feedback for an incident report
     */
    public function storeFeedback(StoreFeedbackRequest $request, IncidentReport $incident_report)
    {
        $this->authorize('provideFeedback', $incident_report);
        
        $incident_report->update([
            // SQD Ratings
            'sqd0_rating' => $request->sqd0_rating,
            'sqd1_rating' => $request->sqd1_rating,
            'sqd2_rating' => $request->sqd2_rating,
            'sqd3_rating' => $request->sqd3_rating,
            'sqd4_rating' => $request->sqd4_rating,
            'sqd5_rating' => $request->sqd5_rating,
            'sqd6_rating' => $request->sqd6_rating,
            'sqd7_rating' => $request->sqd7_rating,
            'sqd8_rating' => $request->sqd8_rating,
            'sqd9_rating' => $request->sqd9_rating,
            'feedback_comment' => $request->feedback_comment,
            'feedback_submitted_at' => now()
        ]);
        
        return back()->with('success', 'Thank you for your feedback!');
    }
    
    /**
     * Mark an incident report as In Progress
     */
    public function markInProgress(IncidentReport $incidentReport)
    {
        $this->authorize('update', $incidentReport);
        
        $oldStatus = $incidentReport->status;
        $incidentReport->update([
            'status' => IncidentReport::STATUS_IN_PROGRESS,
            'staff_notes' => ($incidentReport->staff_notes ?? '') . "\n\nMarked as In Progress by " . auth()->user()->name . " on " . now()->toDateTimeString(),
        ]);

        // Send notification to resident
        if ($incidentReport->user) {
            $incidentReport->user->notify(new \App\Notifications\IncidentReportStatusNotification($incidentReport, $oldStatus, IncidentReport::STATUS_IN_PROGRESS));
        }

        return back()->with('success', 'Incident report marked as In Progress');
    }
    
    /**
     * Mark an incident report as Resolved
     */
    public function markResolved(IncidentReport $incidentReport)
    {
        $this->authorize('update', $incidentReport);
        
        $oldStatus = $incidentReport->status;
        $incidentReport->update([
            'status' => IncidentReport::STATUS_RESOLVED,
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
            'staff_notes' => ($incidentReport->staff_notes ?? '') . "\n\nMarked as Resolved by " . auth()->user()->name . " on " . now()->toDateTimeString(),
        ]);

        // Send notification to resident
        if ($incidentReport->user) {
            $incidentReport->user->notify(new \App\Notifications\IncidentReportStatusNotification($incidentReport, $oldStatus, IncidentReport::STATUS_RESOLVED));
        }

        return back()->with('success', 'Incident report marked as Resolved');
    }

    /**
     * Approve an incident report
     */
    public function approve(IncidentReport $incidentReport)
    {
        $this->authorize('approve', $incidentReport);
        
        $incidentReport->update([
            'status' => IncidentReport::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
            'rejected_by' => null,
            'rejected_at' => null,
            'staff_notes' => ($incidentReport->staff_notes ?? '') . "\n\nApproved by " . auth()->user()->name . " on " . now()->toDateTimeString(),
        ]);

        return back()->with('success', 'Incident report approved successfully');
    }

    /**
     * Reject an incident report
     */
    public function reject(Request $request, IncidentReport $incidentReport)
    {
        $this->authorize('reject', $incidentReport);
        
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
            'additional_notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $incidentReport->status;
        
        // Build comprehensive notes
        $staffNotes = $incidentReport->staff_notes ?? '';
        if ($staffNotes) {
            $staffNotes .= "\n\n";
        }
        $staffNotes .= "Rejected by " . auth()->user()->name . " on " . now()->toDateTimeString();
        if (!empty($validated['additional_notes'])) {
            $staffNotes .= "\nAdditional Notes: " . $validated['additional_notes'];
        }

        $incidentReport->update([
            'status' => IncidentReport::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'approved_by' => null,
            'approved_at' => null,
            'staff_notes' => $staffNotes
        ]);

        // Send notification to resident
        if ($incidentReport->user) {
            $incidentReport->user->notify(new \App\Notifications\IncidentReportStatusNotification($incidentReport, $oldStatus, IncidentReport::STATUS_REJECTED));
        }

        return back()->with('success', 'Incident report rejected and notification sent to resident');
    }
}
