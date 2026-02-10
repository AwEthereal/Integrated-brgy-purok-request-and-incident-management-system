@extends('layouts.app')

@section('title', 'Edit Purok Leader Personal Information')

@section('content')
<div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-6 rounded-lg shadow-lg mb-6">
  <div class="max-w-5xl mx-auto px-4">
    <h1 class="text-2xl md:text-3xl font-bold">Edit Personal Information (RBI Form Style)</h1>
    <p class="text-purple-100 mt-1">Update the details below.</p>
  </div>
</div>

<div class="max-w-5xl mx-auto px-4">
  @if (session('success'))
    <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
  @endif
  @if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 p-4 text-red-800">
      <ul class="list-disc pl-5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('secretary.purok-leaders.personal-info.update', $leader) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <input type="hidden" name="redirect_to" value="{{ old('redirect_to', $redirectTo ?? request('redirect_to', url()->previous())) }}" />

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 md:p-6">
      <div class="flex items-start justify-between gap-4 mb-4">
        <div class="min-w-0">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Purok Leader</h2>
          <p class="text-sm text-gray-600 dark:text-gray-300 truncate">{{ trim(($leader->first_name ?? '') . ' ' . ($leader->last_name ?? '')) ?: $leader->username }}</p>
        </div>
        <a href="{{ $redirectTo ?? request('redirect_to', url()->previous()) ?: route('secretary.purok-leaders.edit', $leader) }}" class="shrink-0 inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">Back</a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Last Name</label>
          <input value="{{ $leader->last_name }}" disabled class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Suffix</label>
          <input name="suffix" value="{{ old('suffix', $leader->suffix) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white" placeholder="Jr., Sr., III" />
          @error('suffix')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">First Name</label>
          <input value="{{ $leader->first_name }}" disabled class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300" />
        </div>
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Middle Name</label>
          <input name="middle_name" value="{{ old('middle_name', $leader->middle_name) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white" />
          @error('middle_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
        <div class="md:col-span-2">
          <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Contact Number</label>
          <input name="contact_number" value="{{ old('contact_number', $leader->contact_number) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white" />
          @error('contact_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Birth Date</label>
          <input type="date" name="birth_date" value="{{ old('birth_date', optional($leader->birth_date)->format('Y-m-d')) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white" />
          @error('birth_date')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Sex</label>
          <select name="sex" class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            <option value="">--</option>
            @foreach(['male' => 'Male', 'female' => 'Female'] as $value => $label)
              <option value="{{ $value }}" @selected(strtolower((string) old('sex', $leader->sex)) === $value)>{{ $label }}</option>
            @endforeach
          </select>
          @error('sex')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Civil Status</label>
          <select name="civil_status" class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            <option value="">--</option>
            @foreach(['Single','Married','Widowed','Separated'] as $opt)
              <option value="{{ $opt }}" @selected(old('civil_status', $leader->civil_status) === $opt)>{{ $opt }}</option>
            @endforeach
          </select>
          @error('civil_status')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Occupation</label>
          <input name="occupation" value="{{ old('occupation', $leader->occupation) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white" />
          @error('occupation')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Address</label>
          <input name="address" value="{{ old('address', $leader->address) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white" />
          @error('address')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      <div class="mt-6 flex items-center justify-end gap-2">
        <a href="{{ $redirectTo ?? request('redirect_to', url()->previous()) ?: route('secretary.purok-leaders.edit', $leader) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</a>
        <button class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500">Save Personal Info</button>
      </div>
    </div>
  </form>
</div>
@endsection
