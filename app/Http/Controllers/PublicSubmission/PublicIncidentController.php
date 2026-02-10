<?php

namespace App\Http\Controllers\PublicSubmission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\IncidentReport;
use App\Models\Purok;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PublicIncidentReceived;
use App\Http\Requests\PublicIncidentRequest;

class PublicIncidentController extends Controller
{
    public function create()
    {
        $incidentTypes = IncidentReport::TYPES;
        $puroks = Purok::orderBy('name')->get();
        return view('incidents.create', compact('incidentTypes', 'puroks'))
            ->with('publicMode', true);
    }

    public function store(PublicIncidentRequest $request)
    {
        // Honeypot: simple hidden field to deter bots
        if ($request->filled('website')) {
            return response()->json(['message' => 'Invalid submission.'], 400);
        }

        $validated = $request->validated();

        // Handle photos (single and multiple) similar to resident flow
        $photoPath = null;
        $photoPaths = [];

        if ($request->filled('photos_data')) {
            try {
                $photosArray = json_decode($request->photos_data, true);
                if (is_array($photosArray)) {
                    foreach ($photosArray as $index => $photoData) {
                        if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
                            $data = substr($photoData, strpos($photoData, ',') + 1);
                            $type = strtolower($type[1]);
                            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) continue;
                            $data = base64_decode($data);
                            if ($data === false) continue;
                            $fileName = 'incident_' . time() . '_' . $index . '.' . $type;
                            $path = 'incident_photos/' . $fileName;
                            \Storage::disk('public')->put($path, $data);
                            $photoPaths[] = $path;
                            if ($index === 0) { $photoPath = $path; }
                        }
                    }
                }
            } catch (\Exception $e) {}
        }
        if (empty($photoPaths) && $request->filled('photo_data')) {
            $data = $request->photo_data;
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]);
                if (in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $data = base64_decode($data);
                    if ($data !== false) {
                        $fileName = 'incident_' . time() . '.' . $type;
                        $photoPath = 'incident_photos/' . $fileName;
                        \Storage::disk('public')->put($photoPath, $data);
                        $photoPaths[] = $photoPath;
                    }
                }
            }
        }
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('incident_photos', 'public');
                $photoPaths[] = $path;
                if ($index === 0 && empty($photoPath)) { $photoPath = $path; }
            }
        }

        $record = IncidentReport::create([
            'user_id' => null,
            'reporter_name' => $validated['reporter_name'],
            'contact_number' => $validated['contact_number'],
            'email' => $validated['email'] ?? null,
            'description' => $validated['description'],
            'location' => $validated['location'] ?? null,
            'status' => IncidentReport::STATUS_PENDING,
            'incident_type' => $request->input('incident_type', 'other'),
            'incident_type_other' => $request->input('incident_type') === 'other' ? ($request->input('incident_type_other') ?: null) : null,
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'photo_path' => $photoPath,
            'photo_paths' => !empty($photoPaths) ? $photoPaths : null,
        ]);

        if (!empty($record->email)) {
            Notification::route('mail', $record->email)
                ->notify(new PublicIncidentReceived($record));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Incident submitted successfully.',
                'id' => $record->id,
            ], 201);
        }

        return redirect()->route('public.thanks');
    }
}
