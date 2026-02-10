@extends('layouts.app')

@section('title', 'Edit Purok Leader')

@section('content')
<div class="py-6">
  <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold mb-4">Edit Purok Leader</h1>

    <form method="POST" action="{{ route('secretary.purok-leaders.update', $leader) }}" class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-700">
      @csrf
      @method('PUT')
      <input type="hidden" name="redirect_to" value="{{ old('redirect_to', request('redirect_to', url()->previous())) }}" />
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
        <input name="username" value="{{ old('username', $leader->username) }}" required
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-3 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
        @error('username')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
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
          <input name="first_name" value="{{ old('first_name', $leader->first_name) }}"
                 class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-3 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
          @error('first_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name</label>
          <input name="last_name" value="{{ old('last_name', $leader->last_name) }}"
                 class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-3 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
          @error('last_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email (optional)</label>
        <input type="email" name="email" value="{{ old('email', $leader->email) }}"
               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-3 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
        @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/30 px-4 py-3">
        <div class="flex items-center justify-between gap-4">
          <div class="min-w-0">
            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">Personal information (RBI Form style)</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">Open the RBI-style template to edit personal details.</div>
          </div>
          <a href="{{ route('purok_leader.resident_records.create', ['user_id' => $leader->id, 'redirect_to' => old('redirect_to', request('redirect_to', url()->previous()))]) }}"
             class="shrink-0 inline-flex items-center px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            Edit RBI
          </a>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assign Purok</label>
        <select name="purok_id" required
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 p-3 text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
          @foreach($puroks as $p)
            <option value="{{ $p->id }}" @selected(old('purok_id', $leader->purok_id) == $p->id)>{{ $p->name }}</option>
          @endforeach
        </select>
        @error('purok_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <!-- Role selection removed; standardized to Purok President -->
      </div>
      <div class="flex items-center justify-end gap-2">
        <a href="{{ request('redirect_to', url()->previous()) ?: route('reports.purok-leaders') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
        <button class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500">Update</button>
      </div>
    </form>
  </div>
</div>
@endsection
