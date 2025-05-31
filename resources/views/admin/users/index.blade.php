@extends('layouts.guest')

@section('content')
<h1 class="text-xl font-semibold mb-4">User Management</h1>

<table class="table-auto w-full border-collapse border border-green-300 bg-green-50">
    <thead>
        <tr class="bg-green-200">
            <th class="border border-green-300 px-3 py-1 text-left text-sm">ID</th>
            <th class="border border-green-300 px-3 py-1 text-left text-sm">Name</th>
            <th class="border border-green-300 px-3 py-1 text-left text-sm">Email</th>
            <th class="border border-green-300 px-3 py-1 text-left text-sm">Role</th>
            <th class="border border-green-300 px-3 py-1 text-left text-sm">Approved</th>
            <th class="border border-green-300 px-3 py-1 text-left text-sm">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr class="hover:bg-green-100">
            <td class="border border-green-300 px-3 py-1 text-sm">{{ $user->id }}</td>
            <td class="border border-green-300 px-3 py-1 text-sm">{{ $user->name }}</td>
            <td class="border border-green-300 px-3 py-1 text-sm">{{ $user->email }}</td>
            <td class="border border-green-300 px-3 py-1 text-sm">{{ $user->role }}</td>
            <td class="border border-green-300 px-3 py-1 text-sm">{{ $user->is_approved ? 'Yes' : 'No' }}</td>
            <td class="border border-green-300 px-3 py-1 text-sm">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-green-700 hover:underline">Edit</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
