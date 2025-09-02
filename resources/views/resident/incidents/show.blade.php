@extends('layouts.app')

@push('styles')

@endpush

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex flex-col space-y-4 mb-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">Incident Report #{{ $report->id }}</h2>
                            <p class="text-sm text-gray-500 mt-1">
                                Reported on {{ $report->created_at->format('F j, Y \a\t h:i A') }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-4">
                            @php
                                $statusClasses = [
                                    'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'In Progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    'Resolved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'Invalid Report' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                ];
                                $displayStatus = $report->getDisplayStatusForResident();
                                $statusClass = $statusClasses[$displayStatus] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                            @endphp
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $displayStatus }}
                            </span>
                            <a href="{{ route('incident_reports.my_reports') }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                                Back to My Reports
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Incident Details</h3>
                        <dl class="space-y-2">
                            <div class="bg-gray-50 px-4 py-3 rounded">
                                <dt class="text-sm font-medium text-gray-500">Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ format_label($report->incident_type) }}</dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 rounded">
                                <dt class="text-sm font-medium text-gray-500">Date Reported</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $report->created_at->format('F j, Y') }}</dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 rounded">
                                <dt class="text-sm font-medium text-gray-500">Time Reported</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $report->created_at->format('h:i A') }}</dd>
                            </div>
                            @if($report->location)
                                <div class="bg-gray-50 px-4 py-3 rounded">
                                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $report->location }}</dd>
                                </div>
                            @endif
                            @if($report->latitude && $report->longitude)
                                <div class="bg-gray-50 px-4 py-3 rounded">
                                    <dt class="text-sm font-medium text-gray-500">Coordinates</dt>
                                    <dd class="mt-1">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs text-gray-500">Latitude</label>
                                                <input type="text" readonly 
                                                       value="{{ $report->latitude }}" 
                                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-gray-100">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500">Longitude</label>
                                                <input type="text" readonly 
                                                       value="{{ $report->longitude }}" 
                                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-gray-100">
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" 
                                               target="_blank" 
                                               class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                View on Google Maps
                                            </a>
                                        </div>
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    @if($report->photo_path)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Photo Evidence</h3>
                            <div class="border rounded-md overflow-hidden">
                                <img src="{{ asset('storage/' . $report->photo_path) }}" 
                                     alt="Incident photo" 
                                     class="w-full h-auto">
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-gray-700 whitespace-pre-line">{{ $report->description }}</p>
                    </div>
                </div>

                @if($report->staff_notes)
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Staff Notes</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p class="whitespace-pre-line">{{ $report->staff_notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Feedback Prompt (shown after report submission) -->
                @if(session('success') && in_array($report->status, ['Pending', 'In Progress']))
                    <div id="feedbackPrompt" class="feedback-prompt">
                        <button type="button" class="feedback-prompt-close" aria-label="Close">&times;</button>
                        <p class="text-gray-800">Thank you for submitting your report! We'll review it shortly.</p>
                        <p class="text-sm text-gray-600 mt-1">You'll be able to provide feedback once it's resolved.</p>
                    </div>
                @endif
                
                <!-- Feedback Form -->
                @if($report->status === 'Resolved' && !$report->feedback_submitted_at)
                    @php
                        // Check if feedback already exists for this incident
                        $hasFeedback = \App\Models\Feedback::where('incident_report_id', $report->id)
                            ->where('user_id', auth()->id())
                            ->exists();
                    @endphp
                    
                    <x-feedback-form 
                        type="incident" 
                        :itemId="$report->id"
                        :hasFeedback="$hasFeedback"
                    />
                @endif

                @if($report->feedback_submitted_at)
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Your Feedback</h3>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-500 mb-3">Submitted on {{ $report->feedback_submitted_at->format('F j, Y \a\t h:i A') }}@if($report->is_anonymous) (Submitted Anonymously)@endif</h4>
                                
                                <div class="space-y-4">
                                    @php
                                        $questions = [
                                            'sqd0_rating' => 'I am satisfied with the service that I availed.',
                                            'sqd1_rating' => 'I spent an acceptable amount of time for my transaction.',
                                            'sqd2_rating' => 'The office accurately informed me and followed the transaction\'s requirements and steps.',
                                            'sqd3_rating' => 'My online transaction (including steps and payment) was simple and convenient.',
                                            'sqd4_rating' => 'I easily found information about my transaction from the office or its website.',
                                            'sqd5_rating' => 'I paid an acceptable amount of fees for my transaction.',
                                            'sqd6_rating' => 'I am confident that my online transaction was secure.',
                                            'sqd7_rating' => 'The office\'s online support was available, or (if asked questions) was quick to respond.',
                                            'sqd8_rating' => 'I got what I needed from the government office.',
                                        ];
                                    @endphp
                                    
                                    @foreach($questions as $field => $question)
                                        <div class="flex items-start">
                                            <div class="flex-1">
                                                <p class="text-sm text-gray-700">{{ $loop->iteration }}. {{ $question }}</p>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                <span class="text-xl" title="{{ \App\Models\IncidentReport::getRatingDescription($report->$field) }}">
                                                    {{ \App\Models\IncidentReport::getRatingEmoji($report->$field) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @if($report->comments)
                                    <div class="mt-6 pt-6 border-t border-gray-200">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Additional Comments:</h4>
                                        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $report->comments }}</p>
                                    </div>
                                @endif
                                
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Overall Satisfaction:</span> 
                                        {{ number_format($report->average_rating, 1) }} out of 5
                                        <span class="text-yellow-500 ml-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($report->average_rating))
                                                    ★
                                                @elseif($i - 0.5 <= $report->average_rating)
                                                    ½
                                                @else
                                                    ☆
                                                @endif
                                            @endfor
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

@endsection
