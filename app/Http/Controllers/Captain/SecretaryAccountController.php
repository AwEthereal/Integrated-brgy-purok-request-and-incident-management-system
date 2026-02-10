<?php

namespace App\Http\Controllers\Captain;

use App\Http\Controllers\Controller;
use App\Models\Purok;
use App\Models\ResidentRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SecretaryAccountController extends Controller
{
    protected array $assignableRoles = ['secretary', 'barangay_kagawad', 'sk_chairman', 'purok_leader'];

    public function index(Request $request)
    {
        $actor = $request->user();
        $query = User::query()
            ->where(function ($q) use ($actor) {
                $q->whereIn('role', $this->assignableRoles);
                if ($actor && $actor->role === 'barangay_captain') {
                    $q->orWhere('id', $actor->id);
                }
            })
            ->withCount('residentRecords');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('username', 'like', "%$s%")
                  ->orWhere('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('name', 'like', "%$s%");
            });
        }
        $secretaries = $query->with('purok')->orderBy('created_at', 'desc')->paginate(15);
        return view('captain.secretaries.index', compact('secretaries'));
    }

    public function create()
    {
        $roles = $this->assignableRoles;
        $puroks = Purok::orderBy('name')->get();
        return view('captain.secretaries.create', compact('roles', 'puroks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in($this->assignableRoles)],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'purok_id' => ['nullable', 'required_if:role,purok_leader', 'exists:puroks,id'],
        ]);

        if (($validated['role'] ?? null) === 'purok_leader') {
            $exists = User::where('role', 'purok_leader')
                ->where('purok_id', $validated['purok_id'])
                ->exists();
            if ($exists) {
                return back()->withErrors(['purok_id' => 'This purok already has a leader assigned.'])->withInput();
            }
        }

        $user = new User();
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->is_approved = 1;
        $user->first_name = $validated['first_name'] ?? $validated['username'];
        $user->last_name = $validated['last_name'] ?? 'Official';
        $user->name = trim(($validated['first_name'] ?? $user->first_name) . ' ' . ($validated['last_name'] ?? $user->last_name));
        if (($validated['role'] ?? null) === 'purok_leader') {
            $user->purok_id = $validated['purok_id'];
        } else {
            $user->purok_id = null;
        }
        $user->save();

        if ($request->boolean('encode_rbi')) {
            $hasRbi = ResidentRecord::where('user_id', $user->id)->whereNull('deleted_at')->exists();
            if (!$hasRbi) {
                return redirect()
                    ->route('purok_leader.resident_records.create', [
                        'user_id' => $user->id,
                        'redirect_to' => route('captain.secretaries.index'),
                    ])
                    ->with('success', 'Account created. Please encode RBI Form B.');
            }
        }

        return redirect()->route('captain.secretaries.index')->with('success', 'Account created.');
    }

    public function edit(Request $request, User $secretary)
    {
        $actor = $request->user();
        $isCaptainSelf = $actor && $actor->role === 'barangay_captain' && (int) $actor->id === (int) $secretary->id && $secretary->role === 'barangay_captain';
        abort_unless(in_array($secretary->role, $this->assignableRoles, true) || $isCaptainSelf, 404);
        $roles = $this->assignableRoles;
        $puroks = Purok::orderBy('name')->get();
        $canEditRole = !$isCaptainSelf;
        return view('captain.secretaries.edit', compact('secretary', 'roles', 'puroks', 'canEditRole'));
    }

    public function update(Request $request, User $secretary)
    {
        $actor = $request->user();
        $isCaptainSelf = $actor && $actor->role === 'barangay_captain' && (int) $actor->id === (int) $secretary->id && $secretary->role === 'barangay_captain';
        abort_unless(in_array($secretary->role, $this->assignableRoles, true) || $isCaptainSelf, 404);
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($secretary->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($secretary->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', Rule::in($isCaptainSelf ? array_merge($this->assignableRoles, ['barangay_captain']) : $this->assignableRoles)],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'purok_id' => ['nullable', 'required_if:role,purok_leader', 'exists:puroks,id'],
        ]);

        if (($validated['role'] ?? null) === 'purok_leader') {
            $exists = User::where('role', 'purok_leader')
                ->where('purok_id', $validated['purok_id'])
                ->where('id', '!=', $secretary->id)
                ->exists();
            if ($exists) {
                return back()->withErrors(['purok_id' => 'This purok already has a leader assigned.'])->withInput();
            }
        }

        $secretary->username = $validated['username'];
        $secretary->email = $validated['email'];
        if (!empty($validated['password'])) {
            $secretary->password = Hash::make($validated['password']);
        }
        $secretary->role = $isCaptainSelf ? 'barangay_captain' : $validated['role'];
        $secretary->first_name = $validated['first_name'] ?? $secretary->first_name;
        $secretary->last_name = $validated['last_name'] ?? $secretary->last_name;
        $secretary->name = trim(($secretary->first_name ?: '') . ' ' . ($secretary->last_name ?: '')) ?: $secretary->username;
        if (($validated['role'] ?? null) === 'purok_leader') {
            $secretary->purok_id = $validated['purok_id'];
        } else {
            $secretary->purok_id = null;
        }
        $secretary->save();

        return redirect()->route('captain.secretaries.index')->with('success', 'Account updated.');
    }

    public function destroy(User $secretary)
    {
        abort_unless(in_array($secretary->role, $this->assignableRoles, true), 404);
        $secretary->delete();
        return redirect()->route('captain.secretaries.index')->with('success', 'Account deleted.');
    }
}
