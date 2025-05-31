@extends('layouts.guest')

@section('content')
<h1 class="text-xl font-semibold mb-4">Edit User: {{ $user->name }}</h1>

<form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    <div>
        <label for="role" class="block mb-1 font-medium">Role</label>
        <select name="role" id="role" required class="w-full rounded border border-green-400 p-2">
            <option value="resident" {{ $user->role == 'resident' ? 'selected' : '' }}>Resident</option>
            <option value="purok_leader" {{ $user->role == 'purok_leader' ? 'selected' : '' }}>Purok Leader</option>
            <option value="barangay_official" {{ $user->role == 'barangay_official' ? 'selected' : '' }}>Barangay Official</option>
            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
    </div>

    <div class="flex items-center">
        <input type="checkbox" name="is_approved" id="is_approved" value="1" class="mr-2" {{ $user->is_approved ? 'checked' : '' }}>
        <label for="is_approved" class="font-medium">Approved</label>
    </div>

    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Update User</button>
</form>

<a href="{{ route('admin.users.index') }}" class="inline-block mt-4 text-green-700 hover:underline">Back to Users</a>
@endsection
