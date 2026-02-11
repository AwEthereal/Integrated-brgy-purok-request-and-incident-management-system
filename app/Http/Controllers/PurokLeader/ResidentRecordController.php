<?php

namespace App\Http\Controllers\PurokLeader;

use App\Http\Controllers\Controller;
use App\Models\ResidentRecord;
use App\Models\Purok;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;

class ResidentRecordController extends Controller
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

    public function __construct()
    {
        $this->authorizeResource(ResidentRecord::class, 'record');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $privRoles = ['secretary','barangay_captain','barangay_kagawad','admin'];
        $isPriv = in_array($user->role, $privRoles);
        $q = trim((string)$request->get('q'));
        $records = ResidentRecord::query()
            ->when(!$isPriv, function ($query) use ($user) {
                $query->where('purok_id', $user->purok_id);
            })
            ->when($q, function ($query) use ($q) {
                $like = "%".$q."%";
                $query->where(function($w) use ($like) {
                    $w->where('last_name', 'like', $like)
                      ->orWhere('first_name', 'like', $like)
                      ->orWhere('middle_name', 'like', $like)
                      ->orWhere('philsys_card_no', 'like', $like)
                      ->orWhere('residence_address', 'like', $like)
                      ->orWhere('contact_number', 'like', $like);
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return view('purok_leader.resident_records.index', compact('records', 'q'));
    }

    public function create(Request $request)
    {
        $religions = ResidentRecord::query()
            ->whereNotNull('religion')
            ->where('religion', '<>', '')
            ->distinct()
            ->orderBy('religion')
            ->pluck('religion');
        $puroks = null;
        $canManageSecFields = in_array($request->user()->role, ['secretary','barangay_captain','barangay_kagawad','admin']);
        if ($canManageSecFields) {
            $puroks = Purok::orderBy('name')->get();
        }
        $secretaries = $canManageSecFields ? User::where('role', 'secretary')->orderBy('name')->get() : collect();
        $prefillUser = null;
        if ($canManageSecFields && $request->filled('user_id')) {
            $prefillUser = User::find((int) $request->query('user_id'));
        }
        if ($prefillUser) {
            $existing = ResidentRecord::where('user_id', $prefillUser->id)->whereNull('deleted_at')->first();
            if ($existing) {
                return redirect()
                    ->route('purok_leader.resident_records.edit', [
                        'record' => $existing->id,
                        'redirect_to' => (string) $request->query('redirect_to', ''),
                    ])
                    ->with('success', 'RBI record already exists for this account.');
            }
        }
        return view('purok_leader.resident_records.create', compact('religions','puroks','secretaries','canManageSecFields','prefillUser'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $this->validateData($request);
        $isPriv = in_array($user->role, ['secretary','barangay_captain','barangay_kagawad','admin']);
        if ($isPriv) {
            $data['purok_id'] = (int) $request->input('purok_id');
            $data['user_id'] = $request->filled('user_id') ? (int) $request->input('user_id') : null;
        } else {
            $data['purok_id'] = $user->purok_id;
            $data['user_id'] = null;
        }
        if (!empty($data['user_id'])) {
            $exists = ResidentRecord::where('user_id', $data['user_id'])->whereNull('deleted_at')->exists();
            if ($exists) {
                return back()->withErrors(['user_id' => 'This account already has an RBI record.'])->withInput();
            }
        }
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        // Education: read two checkboxes; fallback to single-select if present
        $hasCheckboxes = $request->hasAny(['is_graduate','is_undergraduate']);
        if ($hasCheckboxes) {
            $data['is_graduate'] = $request->boolean('is_graduate');
            $data['is_undergraduate'] = $request->boolean('is_undergraduate');
        } else {
            $eduLevel = $request->input('edu_level');
            $data['is_graduate'] = $eduLevel === 'graduate' ? 1 : 0;
            $data['is_undergraduate'] = $eduLevel === 'undergraduate' ? 1 : 0;
        }

        // Secretary/official-only fields
        if ($isPriv) {
            $data['household_number'] = $request->input('household_number');
            $data['attested_by_user_id'] = $request->input('attested_by_user_id');
            $data['is_locked'] = $request->boolean('is_locked');
        }

        $record = ResidentRecord::create($data);

        $this->handleUploads($request, $record);

        $redirectTo = $this->safeRedirectTo($request->input('redirect_to'));
        if (!empty($redirectTo)) {
            return redirect($redirectTo)
                ->with('success', 'Resident record saved successfully!')
                ->with('saved_status', $data['status'] ?? null);
        }

        return redirect()
            ->route('purok_leader.resident_records.create', ['reset' => 1])
            ->with('success', 'Resident record saved successfully!')
            ->with('saved_status', $data['status'] ?? null);
    }

    public function show(ResidentRecord $record)
    {
        return view('purok_leader.resident_records.show', compact('record'));
    }

    public function edit(ResidentRecord $record)
    {
        $religions = ResidentRecord::query()
            ->whereNotNull('religion')
            ->where('religion', '<>', '')
            ->distinct()
            ->orderBy('religion')
            ->pluck('religion');
        $puroks = null;
        $user = request()->user();
        $canManageSecFields = $user && in_array($user->role, ['secretary','barangay_captain','barangay_kagawad','admin']);
        if ($canManageSecFields) {
            $puroks = Purok::orderBy('name')->get();
        }
        $secretaries = $canManageSecFields ? User::where('role', 'secretary')->orderBy('name')->get() : collect();
        return view('purok_leader.resident_records.edit', compact('record', 'religions','puroks','secretaries','canManageSecFields'));
    }

    public function update(Request $request, ResidentRecord $record)
    {
        $user = $request->user();
        $data = $this->validateData($request, $record->id);
        $data['updated_by'] = $user->id;
        // Education: prefer checkboxes; fallback to single-select param
        if ($request->hasAny(['is_graduate','is_undergraduate'])) {
            $data['is_graduate'] = $request->boolean('is_graduate');
            $data['is_undergraduate'] = $request->boolean('is_undergraduate');
        } else {
            $eduLevel = $request->input('edu_level');
            if ($eduLevel !== null) {
                $data['is_graduate'] = $eduLevel === 'graduate' ? 1 : 0;
                $data['is_undergraduate'] = $eduLevel === 'undergraduate' ? 1 : 0;
            }
        }
        if (in_array($user->role, ['secretary','barangay_captain','barangay_kagawad','admin']) && $request->filled('user_id')) {
            $data['user_id'] = (int) $request->input('user_id');
        }
        if (in_array($user->role, ['secretary','barangay_captain','barangay_kagawad','admin']) && $request->filled('purok_id')) {
            $data['purok_id'] = (int) $request->input('purok_id');
        }
        if (in_array($user->role, ['secretary','barangay_captain','barangay_kagawad','admin'])) {
            $data['household_number'] = $request->input('household_number');
            $data['attested_by_user_id'] = $request->input('attested_by_user_id');
            $data['is_locked'] = $request->boolean('is_locked');
        }
        $record->update($data);

        $this->handleUploads($request, $record, true);

        $redirectTo = $this->safeRedirectTo($request->input('redirect_to'));
        if (!empty($redirectTo)) {
            return redirect($redirectTo)->with('success', 'Resident record updated.');
        }

        return redirect()
            ->route('purok_leader.resident_records.show', $record)
            ->with('success', 'Resident record updated.');
    }

    public function destroy(ResidentRecord $record)
    {
        $record->delete();
        $redirect = request()->input('redirect');
        if (is_string($redirect) && $redirect !== '' && str_starts_with($redirect, url('/'))) {
            return redirect($redirect)->with('success', 'Resident record deleted.');
        }
        return redirect()->route('purok_leader.resident_records.index')->with('success', 'Resident record deleted.');
    }

    public function pdf(ResidentRecord $record)
    {
        // Authorization handled by resource policy via authorizeResource
        $pdf = Pdf::loadView('pdf.rbi_form_b', [
            'record' => $record,
        ])->setPaper('A4');

        return $pdf->stream('RBI_Form_B_'.$record->last_name.'_'.$record->first_name.'.pdf');
    }

    protected function validateData(Request $request, ?int $ignoreId = null): array
    {
        $uniquePhilSys = Rule::unique('resident_records', 'philsys_card_no')
            ->whereNull('deleted_at');
        if ($ignoreId) $uniquePhilSys = $uniquePhilSys->ignore($ignoreId);

        $rules = [
            'philsys_card_no' => ['nullable','string','max:64',$uniquePhilSys],
            'last_name' => ['required','string','max:100'],
            'first_name' => ['required','string','max:100'],
            'middle_name' => ['nullable','string','max:100'],
            'suffix' => ['nullable','string','max:20'],
            'birth_date' => ['required','date'],
            'birth_place' => ['nullable','string','max:150'],
            'sex' => ['required','string','max:16'],
            'civil_status' => ['nullable','string','max:32'],
            'religion' => ['nullable','string','max:100'],
            'citizenship' => ['nullable','string','max:100'],
            'residence_address' => ['required','string','max:255'],
            'region' => ['nullable','string','max:100'],
            'province' => ['nullable','string','max:100'],
            'city_municipality' => ['nullable','string','max:100'],
            'barangay' => ['nullable','string','max:100'],
            'occupation' => ['nullable','string','max:150'],
            'contact_number' => ['nullable','string','max:64'],
            'email' => ['nullable','email','max:150'],
            'highest_educ_attainment' => ['nullable','string','in:elementary,high_school,college,post_grad,vocational'],
            'educ_specify' => ['nullable','string','max:150'],
            'edu_level' => ['nullable','string','in:graduate,undergraduate'],
            'is_graduate' => ['nullable','boolean'],
            'is_undergraduate' => ['nullable','boolean'],
            'date_accomplished' => ['nullable','date'],
            // uploads optional for now
            'left_thumbmark' => ['nullable','file','mimes:jpg,jpeg,png,pdf','max:10240'],
            'right_thumbmark' => ['nullable','file','mimes:jpg,jpeg,png,pdf','max:10240'],
            'signature' => ['nullable','file','mimes:jpg,jpeg,png,pdf','max:10240'],
            'status' => ['nullable','string','in:draft,active,archived'],
        ];
        if (in_array($request->user()->role, ['secretary','barangay_captain','barangay_kagawad','admin'])) {
            $rules['purok_id'] = ['required','integer','exists:puroks,id'];
            $rules['user_id'] = ['nullable','integer','exists:users,id', Rule::unique('resident_records', 'user_id')->whereNull('deleted_at')->ignore($ignoreId)];
            $rules['household_number'] = ['nullable','string','max:50'];
            $rules['attested_by_user_id'] = ['nullable','integer','exists:users,id'];
            $rules['is_locked'] = ['nullable','boolean'];
        }
        return $request->validate($rules);
    }

    protected function handleUploads(Request $request, ResidentRecord $record, bool $replace = false): void
    {
        $base = 'resident_records/'.$record->id;
        if ($request->hasFile('left_thumbmark')) {
            if ($replace && $record->left_thumbmark_path) Storage::disk('public')->delete($record->left_thumbmark_path);
            $path = $request->file('left_thumbmark')->store($base, 'public');
            $record->left_thumbmark_path = $path;
        }
        if ($request->hasFile('right_thumbmark')) {
            if ($replace && $record->right_thumbmark_path) Storage::disk('public')->delete($record->right_thumbmark_path);
            $path = $request->file('right_thumbmark')->store($base, 'public');
            $record->right_thumbmark_path = $path;
        }
        if ($request->hasFile('signature')) {
            if ($replace && $record->signature_path) Storage::disk('public')->delete($record->signature_path);
            $path = $request->file('signature')->store($base, 'public');
            $record->signature_path = $path;
        }
        if ($record->isDirty(['left_thumbmark_path','right_thumbmark_path','signature_path'])) {
            $record->save();
        }
    }
}
