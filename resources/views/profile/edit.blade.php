@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-6 rounded-lg shadow-lg mb-4">
            <div class="px-4 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">My Profile</h1>
                    <p class="text-purple-100 mt-1">RBI-style personal information</p>
                </div>
            </div>
        </div>

        @if(isset($user))
            <div class="bg-white rounded-xl shadow border border-gray-200 p-4 md:p-6 mb-8">
                <h2 class="text-lg font-semibold mb-4">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-gray-500">Name</div>
                        <div class="font-medium">{{ $user->name }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Birthdate</div>
                        <div class="font-medium">{{ optional($user->birth_date)->format('Y-m-d') ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Age</div>
                        <div class="font-medium">{{ optional($user->birth_date)->age ? optional($user->birth_date)->age . ' years' : '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Gender</div>
                        <div class="font-medium">{{ $user->gender ? ucfirst(str_replace('_',' ', $user->gender)) : '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Civil Status</div>
                        <div class="font-medium">{{ $user->civil_status ? ucfirst(str_replace('_',' ', $user->civil_status)) : '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Purok</div>
                        <div class="font-medium">
                            @php $purokName = optional(\App\Models\Purok::find($user->purok_id))->name; @endphp
                            {{ $purokName ?: '—' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-gray-500">Occupation</div>
                        <div class="font-medium">{{ $user->occupation ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Contact</div>
                        <div class="font-medium">{{ $user->contact_number ?: '—' }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-gray-500">Address</div>
                        <div class="font-medium">{{ $user->address ?: '—' }}</div>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 text-amber-400 mr-2 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-sm text-amber-800">
                                Your profile is read-only. Please contact the Barangay Secretary or Barangay Captain to request updates.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('dashboard') }}" class="ml-3 inline-flex items-center px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out shadow-sm">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
                    <div id="form-message" class="mt-3 text-sm text-gray-600"></div>
                </div>
            </div>
        @endif

        
    </div>
</div>
@endsection
