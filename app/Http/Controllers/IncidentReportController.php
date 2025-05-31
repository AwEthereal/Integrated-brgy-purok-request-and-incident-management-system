<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncidentReport;
use Illuminate\Support\Facades\Storage;

class IncidentReportController extends Controller
{

    // Resident submits incident report
    public function store(Request $request)
{
    $request->validate([
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

    IncidentReport::create([
        'user_id' => auth()->id(),
        'purok_id' => auth()->user()->purok_id ?? null,
        'description' => $request->description,
        'photo_path' => $photoPath,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'location' => $request->location,
        'status' => 'Pending',
    ]);

    return back()->with('success', 'Incident report submitted.');
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

        return view('admin.incidents.show', compact('report'));
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
            ->orderBy('created_at', 'desc')
            ->get();

        return view('resident.incidents.my_reports', compact('reports'));
    }

    public function create()
    {
        return view('incidents.create'); // Make sure the view exists in resources/views/incidents/create.blade.php
    }
}
