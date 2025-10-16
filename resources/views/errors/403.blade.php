@extends('layouts.guest')

@section('title', 'Access Denied - Barangay Kalawag II')
@section('heading', 'Access Denied')

@section('content')
<div class="text-center">
    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
    
    <h3 class="mt-6 text-xl font-medium text-gray-900">
        Error 403: Forbidden
    </h3>
    
    <p class="mt-2 text-gray-600">
        {{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}
    </p>
    
    <div class="mt-8">
        <a href="{{ url()->previous() }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Go back
        </a>
        
        <div class="mt-4">
            <a href="{{ route('dashboard') }}" class="font-medium text-green-600 hover:text-green-500">
                Or return to dashboard
            </a>
        </div>
    </div>
    
    <div class="mt-8 pt-6 border-t border-gray-200">
        <p class="text-sm text-gray-500">
            Need help? Contact the administrator at 
            <a href="mailto:admin@kalawag2brgy.gov.ph" class="font-medium text-green-600 hover:text-green-500">
                admin@kalawag2brgy.gov.ph
            </a>
        </p>
    </div>
</div>
@endsection
