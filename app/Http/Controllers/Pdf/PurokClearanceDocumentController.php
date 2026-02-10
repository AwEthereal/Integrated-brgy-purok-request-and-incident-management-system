<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Request as RequestModel;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\PurokClearanceApproved;

class PurokClearanceDocumentController extends Controller
{
    protected function authorizePurokLeader(RequestModel $request): void
    {
        $user = Auth::user();
        if (! $user || $user->role !== 'purok_leader') {
            abort(403);
        }
        if ($request->purok_id !== $user->purok_id) {
            abort(403, 'You can only manage documents for your own purok.');
        }
    }

    protected function authorizeOfficials(): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        if (! in_array($user->role, ['secretary', 'barangay_captain', 'barangay_kagawad', 'admin'], true)) {
            abort(403);
        }
    }

    public function preview(RequestModel $request, HttpRequest $httpRequest)
    {
        $this->authorizePurokLeader($request);

        $issueDate = $httpRequest->input('issue_date');
        if (! $issueDate) {
            $issueDate = now()->format('Y-m-d');
        }

        $age = null;
        // Prefer resident-typed age if stored in private notes
        if (!empty($request->purok_private_notes)) {
            try {
                $priv = json_decode($request->purok_private_notes, true);
                if (is_array($priv) && isset($priv['age']) && is_numeric($priv['age'])) {
                    $age = (int) $priv['age'];
                }
            } catch (\Throwable $e) {}
        }
        // Fallback to computing from birth_date if no explicit age available
        if (is_null($age) && $request->birth_date) {
            $age = now()->diffInYears($request->birth_date);
        }
        // Allow override via query when editing preview
        if ($httpRequest->filled('age')) {
            $age = (int) $httpRequest->input('age');
        }

        return view('pdf.clearance_preview', [
            'req' => $request->load(['purok', 'purokLeader']),
            'issue_date' => $issueDate,
            'age' => $age,
        ]);
    }

    public function view(RequestModel $request, HttpRequest $httpRequest)
    {
        $this->authorizePurokLeader($request);

        $issueDate = $httpRequest->input('issue_date');
        if (! $issueDate) {
            $issueDate = now()->format('Y-m-d');
        }

        $age = null;
        if (!empty($request->purok_private_notes)) {
            try {
                $priv = json_decode($request->purok_private_notes, true);
                if (is_array($priv) && isset($priv['age']) && is_numeric($priv['age'])) {
                    $age = (int) $priv['age'];
                }
            } catch (\Throwable $e) {}
        }
        if (is_null($age) && $request->birth_date) {
            $age = now()->diffInYears($request->birth_date);
        }

        return view('pdf.clearance_view', [
            'req' => $request->load(['purok', 'purokLeader']),
            'issue_date' => $issueDate,
            'age' => $age,
        ]);
    }

    public function officialPreview(RequestModel $request, HttpRequest $httpRequest)
    {
        $this->authorizeOfficials();

        $issueDate = $httpRequest->input('issue_date');
        if (! $issueDate) {
            $issueDate = now()->format('Y-m-d');
        }

        $age = null;
        if (!empty($request->purok_private_notes)) {
            try {
                $priv = json_decode($request->purok_private_notes, true);
                if (is_array($priv) && isset($priv['age']) && is_numeric($priv['age'])) {
                    $age = (int) $priv['age'];
                }
            } catch (\Throwable $e) {}
        }
        if (is_null($age) && $request->birth_date) {
            $age = now()->diffInYears($request->birth_date);
        }
        if ($httpRequest->filled('age')) {
            $age = (int) $httpRequest->input('age');
        }

        return view('pdf.clearance_preview', [
            'req' => $request->load(['purok', 'purokLeader']),
            'issue_date' => $issueDate,
            'age' => $age,
            'official_mode' => true,
        ]);
    }

    public function officialUpdateDraft(RequestModel $request, HttpRequest $httpRequest)
    {
        $this->authorizeOfficials();

        $data = $httpRequest->validate([
            'purpose' => ['nullable','string','max:500'],
            'gender' => ['nullable','string','max:20'],
            'address' => ['nullable','string','max:255'],
            'requester_name' => ['nullable','string','max:150'],
            'issue_date' => ['nullable','date'],
            'age' => ['nullable','integer','min:0','max:150'],
        ]);

        $request->update(array_filter([
            'purpose' => $data['purpose'] ?? null,
            'gender' => $data['gender'] ?? null,
            'address' => $data['address'] ?? null,
            'requester_name' => $data['requester_name'] ?? null,
        ], fn($v) => !is_null($v)));

        return redirect()->route('official.clearance.preview', [
            'request' => $request->id,
            'issue_date' => $data['issue_date'] ?? now()->format('Y-m-d'),
            'age' => $data['age'] ?? null,
        ])->with('success', 'Draft updated.');
    }

    public function officialFinalize(RequestModel $request, HttpRequest $httpRequest)
    {
        $this->authorizeOfficials();

        $data = $httpRequest->validate([
            'issue_date' => ['required','date'],
            'age' => ['nullable','integer','min:0','max:150'],
            'purpose' => ['nullable','string','max:500'],
            'gender' => ['nullable','string','max:20'],
            'address' => ['nullable','string','max:255'],
            'requester_name' => ['nullable','string','max:150'],
        ]);

        $issueDate = $httpRequest->input('issue_date');

        $request->update(array_filter([
            'purpose' => $data['purpose'] ?? null,
            'gender' => $data['gender'] ?? null,
            'address' => $data['address'] ?? null,
            'requester_name' => $data['requester_name'] ?? null,
        ], fn($v) => !is_null($v)));

        $age = null;
        if ($request->birth_date) {
            $age = now()->diffInYears($request->birth_date);
        }
        if ($httpRequest->filled('age')) {
            $age = (int) $httpRequest->input('age');
        }

        $pdf = Pdf::loadView('pdf.purok_clearance', [
            'req' => $request->load(['purok','purokLeader']),
            'issue_date' => $issueDate,
            'age' => $age,
        ])->setPaper('A4');

        $path = 'clearances/'.$request->id.'.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        $request->update([
            'status' => 'completed',
            'document_generated_at' => now(),
        ]);

        // Send approval email notification
        $recipientEmail = $request->email ?? optional($request->user)->email;
        
        // Notify linked user if exists
        if ($request->user) {
            $request->user->notify(new \App\Notifications\RequestApprovedNotification($request, 'barangay'));
        }

        // Send email if available
        if ($recipientEmail) {
            try {
                Mail::to($recipientEmail)->send(new PurokClearanceApproved($request, auth()->user()->full_name));
            } catch (\Exception $e) {
                \Log::error('Failed to send approval email: ' . $e->getMessage());
            }
        }

        return redirect()->away(Storage::disk('public')->url($path));
    }

    public function officialView(RequestModel $request, HttpRequest $httpRequest)
    {
        $this->authorizeOfficials();

        $issueDate = $httpRequest->input('issue_date');
        if (! $issueDate) {
            $issueDate = now()->format('Y-m-d');
        }

        $age = null;
        if (!empty($request->purok_private_notes)) {
            try {
                $priv = json_decode($request->purok_private_notes, true);
                if (is_array($priv) && isset($priv['age']) && is_numeric($priv['age'])) {
                    $age = (int) $priv['age'];
                }
            } catch (\Throwable $e) {}
        }
        if (is_null($age) && $request->birth_date) {
            $age = now()->diffInYears($request->birth_date);
        }

        return view('pdf.clearance_view', [
            'req' => $request->load(['purok', 'purokLeader']),
            'issue_date' => $issueDate,
            'age' => $age,
        ]);
    }

    public function updateDraft(RequestModel $request, HttpRequest $httpRequest)
    {
        $this->authorizePurokLeader($request);

        $data = $httpRequest->validate([
            'purpose' => ['nullable','string','max:500'],
            'gender' => ['nullable','string','max:20'],
            'address' => ['nullable','string','max:255'],
            'requester_name' => ['nullable','string','max:150'],
            'issue_date' => ['nullable','date'],
            'age' => ['nullable','integer','min:0','max:150'],
        ]);

        $request->update(array_filter([
            'purpose' => $data['purpose'] ?? null,
            'gender' => $data['gender'] ?? null,
            'address' => $data['address'] ?? null,
            'requester_name' => $data['requester_name'] ?? null,
        ], fn($v) => !is_null($v)));

        // Redirect back to preview with query params to reflect age/issue_date in live preview
        return redirect()->route('purok_leader.clearance.preview', [
            'request' => $request->id,
            'issue_date' => $data['issue_date'] ?? now()->format('Y-m-d'),
            'age' => $data['age'] ?? null,
        ])->with('success', 'Draft updated.');
    }

    public function finalize(RequestModel $request, HttpRequest $httpRequest)
    {
        $this->authorizePurokLeader($request);

        $data = $httpRequest->validate([
            'issue_date' => ['required','date'],
            'age' => ['nullable','integer','min:0','max:150'],
            // Allow persisting on-the-fly edits
            'purpose' => ['nullable','string','max:500'],
            'gender' => ['nullable','string','max:20'],
            'address' => ['nullable','string','max:255'],
            'requester_name' => ['nullable','string','max:150'],
        ]);
        $issueDate = $httpRequest->input('issue_date');

        // Persist edits if provided
        $request->update(array_filter([
            'purpose' => $data['purpose'] ?? null,
            'gender' => $data['gender'] ?? null,
            'address' => $data['address'] ?? null,
            'requester_name' => $data['requester_name'] ?? null,
        ], fn($v) => !is_null($v)));

        $age = null;
        if ($request->birth_date) {
            $age = now()->diffInYears($request->birth_date);
        }
        if ($httpRequest->filled('age')) {
            $age = (int) $httpRequest->input('age');
        }

        $pdf = Pdf::loadView('pdf.purok_clearance', [
            'req' => $request->load(['purok','purokLeader']),
            'issue_date' => $issueDate,
            'age' => $age,
        ])->setPaper('A4');

        $path = 'clearances/'.$request->id.'.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        $request->update([
            'status' => 'completed',
            'document_generated_at' => now(),
        ]);

        // Send approval email notification
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

        // Redirect directly to the generated file so it opens immediately in the browser
        return redirect()->away(Storage::disk('public')->url($path));
    }
}
