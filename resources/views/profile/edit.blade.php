<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow p-6 mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
            <p class="text-gray-500 mt-1">Manage your personal information and account settings</p>
        </div>

        @if(isset($user))
            <div class="grid lg:grid-cols-4 gap-6">
                <!-- Sidebar -->
                <div class="bg-white rounded-xl shadow p-6 lg:col-span-1">
                    <div class="flex flex-col items-center mb-6">
                        @if($user->profile_photo_path)
                            <img class="w-36 h-36 rounded-full object-cover" src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}">
                        @else
                            <div class="w-36 h-36 flex items-center justify-center rounded-full bg-gray-200 text-gray-500 text-4xl font-semibold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <ul class="w-full space-y-2">
                        <li>
                            <a href="#" class="block w-full text-center px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white">Profile Information</a>
                        </li>
                        <li>
                            <a href="{{ route('profile.password.edit') }}" class="block w-full text-center px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-100 text-gray-700">Update Password</a>
                        </li>
                        <li>
                            <a href="#" class="block w-full text-center px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-100 text-gray-700">Account Settings</a>
                        </li>
                        <li>
                            <a href="#" class="block w-full text-center px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-100 text-gray-700">Activity Log</a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-center px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-100 text-gray-700">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>

                <!-- Main content -->
                <div class="bg-white rounded-xl shadow p-6 lg:col-span-3">
                    <h2 class="text-xl font-semibold text-gray-900 border-b pb-3 mb-6">Profile Information</h2>

                    @if(session('status') === 'profile-updated')
                        <div class="bg-green-100 text-green-800 text-sm font-medium px-4 py-3 rounded mb-4 border border-green-200">
                            Profile updated successfully!
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-100 text-red-800 text-sm font-medium px-4 py-3 rounded mb-4 border border-red-200">
                            <ul class="list-disc pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        @else
            <div class="text-red-600 mt-6">
                No user data available.
            </div>
        @endif
    </div>
</body>
</html>
