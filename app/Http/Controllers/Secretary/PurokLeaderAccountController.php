<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Purok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class PurokLeaderAccountController extends Controller
{
    private function safeRedirectTo(?string $redirectTo)
    {
        if (empty($redirectTo)) {
            return null;
        }
        $redirectTo = trim((string) $redirectTo);
        if ($redirectTo === '') {
            return null;
        }

        if (str_starts_with($redirectTo, '/')) {
            return $redirectTo;
        }

        $host = parse_url($redirectTo, PHP_URL_HOST);
        if (!empty($host) && $host === request()->getHost()) {
            return $redirectTo;
        }

        return null;
    }

    public function editPersonalInfo(Request $request, User $purok_leader)
    {
        abort_unless($purok_leader->role === 'purok_leader', 404);

        $redirectTo = $this->safeRedirectTo($request->query('redirect_to'));

        return redirect()->route('purok_leader.resident_records.create', [
            'user_id' => $purok_leader->id,
            'redirect_to' => $redirectTo ?? '',
        ]);
    }

    public function updatePersonalInfo(Request $request, User $purok_leader)
    {
        abort_unless($purok_leader->role === 'purok_leader', 404);

        $redirectTo = $this->safeRedirectTo($request->input('redirect_to'));

        return redirect()->route('purok_leader.resident_records.create', [
            'user_id' => $purok_leader->id,
            'redirect_to' => $redirectTo ?? '',
        ]);
    }

    public function index(Request $request)
    {
        $query = User::with('purok')
            ->where('role', 'purok_leader');
        if ($request->filled('purok_id')) {
            $query->where('purok_id', $request->purok_id);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('username', 'like', "%$s%")
                  ->orWhere('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('name', 'like', "%$s%");
            });
        }
        $leaders = $query->orderBy('created_at', 'desc')->paginate(15);
        $puroks = Purok::orderBy('name')->get();
        return view('secretary.purok_leaders.index', compact('leaders', 'puroks'));
    }

    public function create()
    {
        $puroks = Purok::orderBy('name')->get();
        return view('secretary.purok_leaders.create', compact('puroks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'purok_id' => ['required', 'exists:puroks,id'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => [Schema::hasColumn('users', 'email') ? 'nullable' : 'sometimes', 'email', 'max:255', 'unique:users,email'],
        ]);

        // Ensure only one leader per purok
        $exists = User::where('role', 'purok_leader')
            ->where('purok_id', $validated['purok_id'])
            ->exists();
        if ($exists) {
            return back()->withErrors(['purok_id' => 'This purok already has a leader assigned.'])->withInput();
        }

        $user = new User();
        $user->username = $validated['username'];
        $user->password = Hash::make($validated['password']);
        $user->purok_id = $validated['purok_id'];
        // Standardize to a single role label
        $user->role = 'purok_leader';
        $user->is_approved = 1;
        $user->first_name = $validated['first_name'] ?? $validated['username'];
        $user->last_name = $validated['last_name'] ?? 'PurokLeader';
        $user->name = trim(($validated['first_name'] ?? $user->first_name) . ' ' . ($validated['last_name'] ?? $user->last_name));
        if (Schema::hasColumn('users', 'email')) {
            $user->email = $validated['email'] ?? ($validated['username'] . '@example.test');
        }
        $user->save();

        $redirectTo = $this->safeRedirectTo($request->input('redirect_to'));
        if (!empty($redirectTo)) {
            return redirect($redirectTo)->with('success', 'Purok leader account created.');
        }

        return redirect()->route('reports.purok-leaders')->with('success', 'Purok leader account created.');
    }

    public function edit(User $purok_leader)
    {
        abort_unless($purok_leader->role === 'purok_leader', 404);
        $puroks = Purok::orderBy('name')->get();
        return view('secretary.purok_leaders.edit', ['leader' => $purok_leader, 'puroks' => $puroks]);
    }

    public function update(Request $request, User $purok_leader)
    {
        abort_unless($purok_leader->role === 'purok_leader', 404);
        $rules = [
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($purok_leader->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'purok_id' => ['required', 'exists:puroks,id'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => [Schema::hasColumn('users', 'email') ? 'nullable' : 'sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($purok_leader->id)],
        ];

        // Optional personal info fields (RBI-style) - validate only if columns exist
        if (Schema::hasColumn('users', 'middle_name')) {
            $rules['middle_name'] = ['nullable', 'string', 'max:255'];
        }
        if (Schema::hasColumn('users', 'suffix')) {
            $rules['suffix'] = ['nullable', 'string', 'max:50'];
        }
        if (Schema::hasColumn('users', 'contact_number')) {
            $rules['contact_number'] = ['nullable', 'string', 'max:64'];
        }
        if (Schema::hasColumn('users', 'birth_date')) {
            $rules['birth_date'] = ['nullable', 'date'];
        }
        if (Schema::hasColumn('users', 'date_of_birth')) {
            $rules['date_of_birth'] = ['nullable', 'date'];
        }
        if (Schema::hasColumn('users', 'sex')) {
            $rules['sex'] = ['nullable', 'string', Rule::in(['male', 'female', 'Male', 'Female'])];
        }
        if (Schema::hasColumn('users', 'gender')) {
            $rules['gender'] = ['nullable', 'string', Rule::in(['male', 'female', 'other', 'Male', 'Female', 'Other'])];
        }
        if (Schema::hasColumn('users', 'civil_status')) {
            $rules['civil_status'] = ['nullable', 'string', 'max:50'];
        }
        if (Schema::hasColumn('users', 'occupation')) {
            $rules['occupation'] = ['nullable', 'string', 'max:150'];
        }
        if (Schema::hasColumn('users', 'address')) {
            $rules['address'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $normalizedSex = null;
        if (array_key_exists('sex', $validated) && $validated['sex'] !== null && $validated['sex'] !== '') {
            $sex = strtolower((string) $validated['sex']);
            $normalizedSex = in_array($sex, ['male', 'female'], true) ? $sex : null;
        }

        $normalizedGender = null;
        if (array_key_exists('gender', $validated) && $validated['gender'] !== null && $validated['gender'] !== '') {
            $gender = strtolower((string) $validated['gender']);
            $normalizedGender = in_array($gender, ['male', 'female', 'other'], true) ? $gender : null;
        }

        $normalizedBirthDate = null;
        if (!empty($validated['birth_date'] ?? null)) {
            $normalizedBirthDate = $validated['birth_date'];
        } elseif (!empty($validated['date_of_birth'] ?? null)) {
            $normalizedBirthDate = $validated['date_of_birth'];
        }

        // Ensure only one leader per purok (exclude current)
        $exists = User::where('role', 'purok_leader')
            ->where('purok_id', $validated['purok_id'])
            ->where('id', '!=', $purok_leader->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['purok_id' => 'This purok already has a leader assigned.'])->withInput();
        }

        $purok_leader->username = $validated['username'];
        if (!empty($validated['password'])) {
            $purok_leader->password = Hash::make($validated['password']);
        }
        $purok_leader->purok_id = $validated['purok_id'];
        // Keep standardized role
        $purok_leader->role = 'purok_leader';
        $purok_leader->first_name = $validated['first_name'] ?? $purok_leader->first_name;
        $purok_leader->last_name = $validated['last_name'] ?? $purok_leader->last_name;
        $purok_leader->name = trim(($purok_leader->first_name ?: '') . ' ' . ($purok_leader->last_name ?: '')) ?: $purok_leader->username;

        // Persist optional personal info fields if present
        if (Schema::hasColumn('users', 'middle_name') && array_key_exists('middle_name', $validated)) {
            $purok_leader->middle_name = $validated['middle_name'];
        }
        if (Schema::hasColumn('users', 'suffix') && array_key_exists('suffix', $validated)) {
            $purok_leader->suffix = $validated['suffix'];
        }
        if (Schema::hasColumn('users', 'contact_number') && array_key_exists('contact_number', $validated)) {
            $purok_leader->contact_number = $validated['contact_number'];
        }
        if ($normalizedBirthDate !== null) {
            if (Schema::hasColumn('users', 'birth_date')) {
                $purok_leader->birth_date = $normalizedBirthDate;
            }
            if (Schema::hasColumn('users', 'date_of_birth')) {
                $purok_leader->date_of_birth = $normalizedBirthDate;
            }
        }
        if (Schema::hasColumn('users', 'sex') && array_key_exists('sex', $validated)) {
            $purok_leader->sex = $normalizedSex;
        }
        if (Schema::hasColumn('users', 'gender') && array_key_exists('gender', $validated)) {
            $purok_leader->gender = $normalizedGender;
        }

        // Compatibility: if only one of sex/gender was provided, keep them aligned
        if (Schema::hasColumn('users', 'sex') && Schema::hasColumn('users', 'gender')) {
            if ($normalizedSex !== null && $normalizedGender === null) {
                $purok_leader->gender = $normalizedSex;
            }
            if ($normalizedGender !== null && $normalizedSex === null && in_array($normalizedGender, ['male', 'female'], true)) {
                $purok_leader->sex = $normalizedGender;
            }
        }
        if (Schema::hasColumn('users', 'civil_status') && array_key_exists('civil_status', $validated)) {
            $purok_leader->civil_status = $validated['civil_status'];
        }
        if (Schema::hasColumn('users', 'occupation') && array_key_exists('occupation', $validated)) {
            $purok_leader->occupation = $validated['occupation'];
        }
        if (Schema::hasColumn('users', 'address') && array_key_exists('address', $validated)) {
            $purok_leader->address = $validated['address'];
        }
        if (Schema::hasColumn('users', 'email')) {
            if (!empty($validated['email'])) {
                $purok_leader->email = $validated['email'];
            } elseif (empty($purok_leader->email)) {
                $purok_leader->email = $validated['username'] . '@example.test';
            }
        }
        $purok_leader->save();

        $redirectTo = $this->safeRedirectTo($request->input('redirect_to'));
        if (!empty($redirectTo)) {
            return redirect($redirectTo)->with('success', 'Account updated.');
        }

        return redirect()->route('reports.purok-leaders')->with('success', 'Account updated.');
    }

    public function destroy(User $purok_leader)
    {
        abort_unless($purok_leader->role === 'purok_leader', 404);
        $purok_leader->delete();
        return redirect()->route('secretary.purok-leaders.index')->with('success', 'Account deleted.');
    }
}
