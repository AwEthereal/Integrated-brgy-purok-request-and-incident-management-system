<?php

namespace App\Http\Controllers\PublicSubmission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\Request as ServiceRequest;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\PublicClearanceRequest;
use App\Models\Purok;

class PublicClearanceController extends Controller
{
    public function create()
    {
        $puroks = Purok::orderBy('name')->get(['id','name']);
        return view('public.clearance', compact('puroks'));
    }

    public function store(PublicClearanceRequest $request)
    {
        // Honeypot: simple hidden field to deter bots
        if ($request->filled('website')) {
            return response()->json(['message' => 'Invalid submission.'], 400);
        }

        $validated = $request->validated();
        // If age was provided, derive an approximate birth_date so downstream PDF can compute age automatically
        $derivedBirthDate = null;
        if (isset($validated['age']) && is_numeric($validated['age'])) {
            $derivedBirthDate = now()->subYears((int) $validated['age'])->toDateString();
        }

        $frontPath = null;
        $backPath = null;
        $facePath = null;
        if ($request->hasFile('valid_id_front')) {
            $frontPath = $request->file('valid_id_front')->store('requests/ids', 'public');
        }
        if ($request->hasFile('valid_id_back')) {
            $backPath = $request->file('valid_id_back')->store('requests/ids', 'public');
        }
        if ($request->hasFile('face_photo')) {
            $facePath = $request->file('face_photo')->store('requests/face', 'public');
        }

        $record = ServiceRequest::create([
            'user_id' => null,
            'form_type' => 'barangay_clearance',
            'purpose' => $validated['purpose'],
            'status' => 'pending',
            'purok_id' => $validated['purok_id'] ?? null,
            'requester_name' => $validated['requester_name'],
            'contact_number' => $validated['contact_number'],
            'email' => $validated['email'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
            'birth_date' => $derivedBirthDate,
            // Store the exact typed age non-destructively for leader preview (if provided)
            'purok_private_notes' => isset($validated['age']) && is_numeric($validated['age'])
                ? json_encode(['age' => (int) $validated['age']])
                : null,
            // If face photo provided, we store it in the front path field
            'valid_id_front_path' => $facePath ?: $frontPath,
            'valid_id_back_path' => $backPath,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Request submitted successfully.',
                'id' => $record->id,
            ], 201);
        }

        return redirect()->route('public.thanks');
    }
}
