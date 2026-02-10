@extends('layouts.app')

@section('title', 'Submit Feedback')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-600 text-white p-6">
            <h1 class="text-2xl font-bold">{{ $title ?? 'Feedback Form' }}</h1>
            <p class="mt-2">{{ $description ?? 'We value your feedback to help us improve our services.' }}</p>
        </div>

        <form action="{{ $formAction ?? route('feedback.store') }}" method="POST" class="p-6" id="feedback-form">
            @csrf
            <input type="hidden" name="item_type" value="{{ $itemType ?? '' }}">
            <input type="hidden" name="item_id" value="{{ $itemId ?? '' }}">
            @if(($isPublic ?? false) === true)
                <input type="hidden" name="is_public" value="1">
            @endif
            
            <div class="space-y-6">
                <!-- Service Quality Dimensions -->
                <div class="space-y-4">
                    <h2 class="text-lg font-medium text-gray-900">Please rate your experience:</h2>
                    
                    @php
                        $questions = [
                            'sqd0_rating' => 'I am satisfied with the service I received.',
                            'sqd1_rating' => 'Submitting my request or report took an acceptable amount of time.',
                            'sqd2_rating' => 'The office clearly explained and followed the service requirements and steps.',
                            'sqd3_rating' => 'The process for submitting requests or reports was simple and convenient.',
                            'sqd4_rating' => 'I easily obtained the information I needed from the kiosk.',
                            'sqd5_rating' => 'The process for accessing the service was straightforward.',
                            'sqd6_rating' => 'I am confident that using the system was secure.',
                            'sqd7_rating' => 'The office staff responded quickly when I asked questions.',
                            'sqd8_rating' => 'I received what I needed from the office.',
                        ];
                        
                        $emojiData = [
                            1 => ['emoji' => '😠', 'label' => 'Strongly Disagree'],
                            2 => ['emoji' => '😞', 'label' => 'Disagree'],
                            3 => ['emoji' => '😐', 'label' => 'Neutral'],
                            4 => ['emoji' => '😊', 'label' => 'Agree'],
                            5 => ['emoji' => '😍', 'label' => 'Strongly Agree']
                        ];
                    @endphp

                    @foreach($questions as $field => $question)
                        <div class="border-b border-gray-200 pb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">{{ $question }}</label>
                            <div class="flex justify-between items-center space-x-2">
                                @foreach($emojiData as $value => $data)
                                    <label class="flex-1 text-center cursor-pointer">
                                        <input type="radio" 
                                               name="{{ $field }}" 
                                               value="{{ $value }}" 
                                               class="sr-only" 
                                               {{ old($field) == $value ? 'checked' : '' }}
                                               required>
                                        <div class="emoji-option p-2 rounded-lg hover:bg-gray-100 transition-all duration-200">
                                            <div class="text-3xl mb-1">{{ $data['emoji'] }}</div>
                                            <div class="text-xs text-gray-600">{{ $data['label'] }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error($field)
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>

                <!-- Additional Comments -->
                <div>
                    <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">Additional Comments (Optional)</label>
                    <textarea id="comments" 
                              name="comments" 
                              rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                              placeholder="Please share any additional comments or suggestions...">{{ old('comments') }}</textarea>
                </div>

                <!-- Anonymous Feedback Option -->
                <div class="flex items-center">
                    <input id="is_anonymous" 
                           name="is_anonymous" 
                           type="checkbox" 
                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                           {{ old('is_anonymous') ? 'checked' : '' }}>
                    <label for="is_anonymous" class="ml-2 block text-sm text-gray-700">
                        Submit feedback anonymously
                    </label>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center pt-6">
                    @if(($showSkip ?? true) === true)
                        <button type="button" 
                                id="skip-feedback" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Skip for Now
                        </button>
                    @else
                        <div></div>
                    @endif
                    <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Submit Feedback
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle skip feedback
        const skipBtn = document.getElementById('skip-feedback');
        if (!skipBtn) return;
        skipBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show confirmation dialog
            if (confirm('Are you sure you want to skip providing feedback? You can always provide it later.')) {
                // Submit skip request via AJAX
                fetch('{{ route("feedback.skip") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        type: '{{ $itemType }}',
                        id: '{{ $itemId }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.href = '{{ route("dashboard") }}';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.href = '{{ route("dashboard") }}';
                });
            }
        });

        // Form validation
        const form = document.getElementById('feedback-form');
        form.addEventListener('submit', function(e) {
            // Client-side validation can be added here if needed
            // The server-side validation will handle any issues
        });
    });
</script>
@endpush

@endsection
