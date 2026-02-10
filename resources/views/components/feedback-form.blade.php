@php
    $emojiData = [
        1 => ['emoji' => '😠', 'label' => 'Strongly Disagree'],
        2 => ['emoji' => '😞', 'label' => 'Disagree'],
        3 => ['emoji' => '😐', 'label' => 'Neutral'],
        4 => ['emoji' => '😊', 'label' => 'Agree'],
        5 => ['emoji' => '😍', 'label' => 'Strongly Agree']
    ];
    
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
    
    $type = $type ?? 'request';
    $itemId = $itemId ?? null;
    $hasFeedback = $hasFeedback ?? false;
@endphp

@if(!$hasFeedback)
<div class="mt-8 bg-white rounded-lg shadow-md p-6" x-data="{
    showForm: false,
    isSubmitting: false,
    ratings: Array(9).fill(3), // Initialize all ratings to 3 (Neutral)
    comments: '',
    isAnonymous: false,
    errors: {},
    
    async submitFeedback() {
        this.isSubmitting = true;
        this.errors = {};
        
        console.log('Current ratings:', this.ratings);
        
        // Ensure all ratings are provided with at least a default value of 3 (Neutral)
        const ratingsArray = [];
        for (let i = 0; i < 9; i++) {
            ratingsArray[i] = this.ratings[i] !== undefined ? this.ratings[i] : 3; // Default to 3 (Neutral) if not rated
        }
        
        console.log('Submitting ratings:', ratingsArray);
        
        try {
            const response = await fetch('{{ route('feedback.submit') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    type: '{{ $type }}',
                    id: '{{ $itemId }}',
                    ratings: ratingsArray,
                    comments: this.comments,
                    is_anonymous: this.isAnonymous
                })
            });
            
            const data = await response.json();
            console.log('Response:', { status: response.status, data });
            
            if (response.ok) {
                // Show success message and reload after a short delay
                alert('Thank you for your feedback!');
                window.location.reload();
            } else {
                if (response.status === 422) {
                    this.errors = data.errors || {};
                    console.error('Validation errors:', this.errors);
                    alert('Please complete all required fields before submitting.');
                } else {
                    const errorMsg = data.message || 'An error occurred while submitting your feedback.';
                    console.error('Submission error:', errorMsg);
                    alert(errorMsg);
                }
            }
        } catch (error) {
            console.error('Error submitting feedback:', error);
            alert('Failed to connect to the server. Please check your internet connection and try again.');
        } finally {
            this.isSubmitting = false;
        }
    }
}">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Share Your Feedback</h3>
        <button @click="showForm = !showForm" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <span x-text="showForm ? 'Hide Feedback Form' : 'Leave Feedback'"></span>
        </button>
    </div>
    
    <div x-show="showForm" x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         class="mt-4">
        <div class="space-y-6">
            <!-- Service Quality Dimensions -->
            <div class="space-y-4">
                @php $questionIndex = 0; @endphp
                @foreach($questions as $field => $question)
                    <div class="border-b border-gray-200 pb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $question }}</label>
                        <div class="flex justify-between items-center space-x-2">
                            @foreach($emojiData as $value => $data)
                                <label class="flex-1 text-center cursor-pointer" 
                                       @click="ratings[{{ $questionIndex }}] = {{ $value }}">
                                    <div class="emoji-option p-2 rounded-lg transition-all duration-200"
                                         :class="{ 'bg-gray-100': ratings[{{ $questionIndex }}] === {{ $value }} }">
                                        <div class="text-2xl mb-1">{{ $data['emoji'] }}</div>
                                        <div class="text-xs text-gray-600">{{ $data['label'] }}</div>
                                    </div>
                                    <input type="radio" name="ratings[{{ $questionIndex }}]" class="hidden" 
                                           x-model="ratings[{{ $questionIndex }}]" value="{{ $value }}">
                                </label>
                            @endforeach
                        </div>
                        <template x-if="errors['{{ str_replace('_rating', '', $field) }}']">
                            <p class="mt-1 text-sm text-red-600" x-text="errors['{{ str_replace('_rating', '', $field) }}'][0]"></p>
                        </template>
                    </div>
                    @php $questionIndex++; @endphp
                @endforeach
            </div>
            
            <!-- Additional Comments -->
            <div class="mt-4">
                <label for="comments" class="block text-sm font-medium text-gray-700">Additional comments (optional)</label>
                <div class="mt-1">
                    <textarea id="comments" x-model="comments" rows="3" 
                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                </div>
            </div>
            
            <!-- Anonymous Toggle -->
            <div class="flex items-center">
                <input id="is_anonymous" type="checkbox" x-model="isAnonymous" 
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_anonymous" class="ml-2 block text-sm text-gray-700">
                    Submit feedback anonymously
                </label>
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="button" @click="submitFeedback()" :disabled="isSubmitting"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                    <span x-show="!isSubmitting">Submit Feedback</span>
                    <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@else
<div class="mt-8 bg-green-50 border-l-4 border-green-400 p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-green-700">
                Thank you for your feedback! Your input helps us improve our services.
            </p>
        </div>
    </div>
</div>
@endif
