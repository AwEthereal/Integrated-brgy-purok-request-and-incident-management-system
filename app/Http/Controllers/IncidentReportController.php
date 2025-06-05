<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncidentReport;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Purok;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class IncidentReportController extends Controller
{

    // Resident submits incident report
    public function store(Request $request)
    {
        $request->validate([
            'incident_type' => 'required|in:' . implode(',', array_keys(\App\Models\IncidentReport::TYPES)),
            'description' => 'required|string',
            'photo_data' => 'nullable|string', // base64 string
            'photo' => 'nullable|image|max:2048', // fallback file upload
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location' => 'nullable|string'
        ]);

    $photoPath = null;

    if ($request->filled('photo_data')) {
        // Extract base64 string from data URL
        $data = $request->photo_data;
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                return back()->withErrors(['photo_data' => 'Invalid image type']);
            }

            $data = base64_decode($data);

            if ($data === false) {
                return back()->withErrors(['photo_data' => 'Base64 decode failed']);
            }
        } else {
            return back()->withErrors(['photo_data' => 'Invalid image data']);
        }

        $fileName = 'incident_' . time() . '.' . $type;
        $photoPath = 'incident_photos/' . $fileName;

        Storage::disk('public')->put($photoPath, $data);
    } elseif ($request->hasFile('photo')) {
        $photoPath = $request->file('photo')->store('incident_photos', 'public');
    }

    $incident = IncidentReport::create([
        'user_id' => auth()->id(),
        'purok_id' => auth()->user()->purok_id ?? null,
        'incident_type' => $request->incident_type,
        'description' => $request->description,
        'photo_path' => $photoPath,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'location' => $request->location,
        'status' => 'Pending',
    ]);

    return redirect()->route('incident_reports.show', $incident->id)
        ->with('success', 'Incident report submitted successfully!');
}



    // Staff views all reports
    public function index()
    {
        $reports = IncidentReport::with(['user', 'purok'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.incidents.index', compact('reports'));
    }

    // Staff views a specific report
    public function show($id)
    {
        $report = IncidentReport::with(['user', 'purok'])->findOrFail($id);
        
        // Check if the authenticated user is an admin
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'barangay_official') {
            return view('admin.incidents.show', compact('report'));
        }
        
        // For residents, show the resident view
        return view('resident.incidents.show', compact('report'));
    }

    // Staff updates status or adds notes
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'staff_notes' => 'nullable|string',
        ]);

        $report = IncidentReport::findOrFail($id);
        $report->update([
            'status' => $request->status,
            'staff_notes' => $request->staff_notes,
        ]);

        return back()->with('success', 'Report updated.');
    }

    // Optional: Resident views their own submitted reports
    public function myReports()
    {
        $reports = IncidentReport::where('user_id', auth()->id())
            ->when(request('type'), function($query, $type) {
                return $query->where('incident_type', $type);
            })
            ->when(request('status'), function($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(request()->query());

        return view('resident.incidents.my_reports', compact('reports'));
    }
    
    /**
     * Store feedback for an incident report
     */
    public function storeFeedback(StoreFeedbackRequest $request, IncidentReport $incident_report)
    {
        // The request is already validated by StoreFeedbackRequest
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
            'comments' => $request->comments,
            'is_anonymous' => $request->is_anonymous ?? false,
            'feedback_submitted_at' => now(),
        ]);

        return redirect()->route('incident_reports.show', $incident_report)
            ->with('success', 'Thank you for your feedback! Your responses have been recorded.');
    }

    public function create()
    {
        return view('incidents.create');
    }
}
