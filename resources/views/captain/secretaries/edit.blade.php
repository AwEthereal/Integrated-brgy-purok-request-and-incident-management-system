@extends('layouts.app')

@section('title', 'Edit Official Account')

@section('content')
<div class="py-6">
  <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold mb-4">Edit Official Account</h1>

    <form method="POST" action="{{ route('captain.secretaries.update', $secretary) }}" class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-700">
      @csrf
      @method('PUT')
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
        <input name="username" value="{{ old('username', $secretary->username) }}" required
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-3 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
        @error('username')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $secretary->email) }}" required
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-3 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
        @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password (optional)</label>
        <input type="password" name="password"
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-3 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
        @error('password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
          <input name="first_name" value="{{ old('first_name', $secretary->first_name) }}"
                 class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-3 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
          @error('first_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name</label>
          <input name="last_name" value="{{ old('last_name', $secretary->last_name) }}"
                 class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-3 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
          @error('last_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
      @php($canEditRole = $canEditRole ?? true)
      @if($canEditRole)
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
          <select id="roleSelect" name="role" required
                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-3 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <option value="secretary" @selected(old('role', $secretary->role)==='secretary')>Secretary</option>
            <option value="barangay_kagawad" @selected(old('role', $secretary->role)==='barangay_kagawad')>Barangay Kagawad</option>
            <option value="sk_chairman" @selected(old('role', $secretary->role)==='sk_chairman')>SK Chairman</option>
            <option value="purok_leader" @selected(old('role', $secretary->role)==='purok_leader')>Purok Leader</option>
          </select>
          @error('role')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
      @else
        <input type="hidden" name="role" value="barangay_captain" />
      @endif

      <div id="purokField" class="hidden">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assign Purok</label>
        <select id="purokSelect" name="purok_id"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-3 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
          <option value="">Select Purok</option>
          @foreach($puroks as $p)
            <option value="{{ $p->id }}" @selected(old('purok_id', $secretary->purok_id) == $p->id)>{{ $p->name }}</option>
          @endforeach
        </select>
        @error('purok_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div class="flex items-center justify-end gap-2">
        <a href="{{ route('captain.secretaries.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
        <button class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500">Update</button>
      </div>
    </form>

    <script>
      (function () {
        var roleSelect = document.getElementById('roleSelect');
        var purokField = document.getElementById('purokField');
        var purokSelect = document.getElementById('purokSelect');

        function syncPurokVisibility() {
          var isLeader = roleSelect && roleSelect.value === 'purok_leader';
          if (purokField) {
            purokField.classList.toggle('hidden', !isLeader);
          }
          if (purokSelect) {
            purokSelect.required = !!isLeader;
          }
        }

        if (roleSelect) {
          roleSelect.addEventListener('change', syncPurokVisibility);
        }
        syncPurokVisibility();
      })();
    </script>
  </div>
</div>
@endsection
