@php
    // Debug information
    $showFeedbackPrompt = session('show_feedback_prompt', false);
    $pendingFeedback = session('pending_feedback');
    $feedbackSubmitted = request()->cookie('feedback_submitted', false);
    $feedbackSkipped = request()->cookie('feedback_skipped', false);
    $isAuthenticated = auth()->check();
    
    // Output debug information
    echo '<!-- Feedback Prompt Debug -->';
    echo '<!-- show_feedback_prompt: ' . ($showFeedbackPrompt ? 'true' : 'false') . ' -->';
    echo '<!-- pending_feedback: ' . ($pendingFeedback ? 'exists' : 'none') . ' -->';
    echo '<!-- feedback_submitted cookie: ' . ($feedbackSubmitted ? 'true' : 'false') . ' -->';
    echo '<!-- feedback_skipped cookie: ' . ($feedbackSkipped ? 'true' : 'false') . ' -->';
    echo '<!-- auth check: ' . ($isAuthenticated ? 'logged in as ' . auth()->user()->email : 'not logged in') . ' -->';
    
    if ($pendingFeedback) {
        echo '<!-- Pending feedback data: ' . json_encode($pendingFeedback) . ' -->';
    }
    
    // Check if we should show the feedback prompt
    $shouldShowPrompt = $showFeedbackPrompt && $pendingFeedback && !$feedbackSubmitted && !$feedbackSkipped;
@endphp

@if($shouldShowPrompt && $pendingFeedback)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="show = false" x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                x-data="{
                    show: true,
                    isSubmitting: false,
                    ratings: {},
                    comments: '',
                    isAnonymous: false,
                    errors: {},
                    
                    init() {
                        // Initialize ratings for each SQD
                        @for($i = 0; $i < 5; $i++)
                            this.ratings[{{ $i }}] = 0;
                        @endfor
                    },
                    
                    async submitFeedback() {
                        this.isSubmitting = true;
                        this.errors = {};
                        
                        // Validate ratings
                        let isValid = true;
                        for (let i = 0; i < 5; i++) {
                            if (!this.ratings[i] || this.ratings[i] < 1 || this.ratings[i] > 5) {
                                this.errors[`sqd${i}_rating`] = ['Please provide a rating.'];
                                isValid = false;
                            }
                        }
                        
                        if (!isValid) {
                            this.isSubmitting = false;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                            return;
                        }
                        
                        try {
                            const response = await fetch('{{ route('feedback.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    item_type: '{{ session('pending_feedback.type') }}',
                                    item_id: '{{ session('pending_feedback.id') }}',
                                    comments: this.comments,
                                    is_anonymous: this.isAnonymous,
                                    sqd0_rating: this.ratings[0],
                                    sqd1_rating: this.ratings[1],
                                    sqd2_rating: this.ratings[2],
                                    sqd3_rating: this.ratings[3],
                                    sqd4_rating: this.ratings[4]
                                })
                            });

                            if (!response.ok) {
                                const data = await response.json();
                                if (response.status === 422 && data.errors) {
                                    // Handle validation errors
                                    this.errors = data.errors;
                                    return;
                                }
                                throw new Error('Network response was not ok');
                            }

                            // Reload the page to show success message
                            window.location.reload();
                            
                        } catch (error) {
                            console.error('Error submitting feedback:', error);
                            alert('An error occurred while submitting your feedback. Please try again.');
                        } finally {
                            this.isSubmitting = false;
                        }
                    },
                    
                    async skipFeedback() {
                        this.isSubmitting = true;
                        try {
                            const response = await fetch('{{ route('feedback.skip') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    item_type: '{{ session('pending_feedback.type') }}',
                                    item_id: '{{ session('pending_feedback.id') }}'
                                })
                            });

                            if (!response.ok) {
                                throw new Error('Failed to skip feedback');
                            }

                            // Set a cookie to prevent showing the feedback prompt again
                            document.cookie = 'feedback_skipped=true; path=/; max-age=86400; SameSite=Lax';
                            
                            // Show success message
                            const event = new CustomEvent('toast', {
                                detail: {
                                    message: 'Feedback skipped. You can provide feedback later from your profile.',
                                    type: 'info'
                                }
                            });
                            window.dispatchEvent(event);
                            
                            // Close the modal
                            this.show = false;
                            
                        } catch (error) {
                            console.error('Error skipping feedback:', error);
                            alert('An error occurred while skipping feedback. Please try again.');
                        } finally {
                            this.isSubmitting = false;
                        }
                    }
                }"
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            We'd love your feedback!
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Please take a moment to rate your experience with this {{ session('pending_feedback.type') }}.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Error messages -->
                <template x-for="(error, key) in errors" :key="key">
                    <p class="mt-2 text-sm text-red-600" x-text="error[0]"></p>
                </template>
                
                <!-- Feedback Form -->
                <div class="mt-6">
                    <div class="space-y-6">
                        <!-- Service Quality Dimensions -->
                        <div class="space-y-4">
                            <h2 class="text-lg font-medium text-gray-900">Please rate your experience:</h2>
                            
                            @php
                                $questions = [
                                    'sqd0_rating' => 'I am satisfied with the service that I availed.',
                                    'sqd1_rating' => 'I spent an acceptable amount of time for my transaction.',
                                    'sqd2_rating' => 'The office accurately informed me and followed the transaction\'s requirements and steps.',
                                    'sqd3_rating' => 'My online transaction (including steps and payment) was simple and convenient.',
                                    'sqd4_rating' => 'I easily found information about my transaction from the office or its website.'
                                ];
                                
                                $emojiData = [
                                    1 => ['emoji' => '😠', 'label' => 'Strongly Disagree'],
                                    2 => ['emoji' => '😞', 'label' => 'Disagree'],
                                    3 => ['emoji' => '😐', 'label' => 'Neutral'],
                                    4 => ['emoji' => '😊', 'label' => 'Agree'],
                                    5 => ['emoji' => '😍', 'label' => 'Strongly Agree']
                                ];
                            @endphp

                            @php $questionIndex = 0; @endphp
                            @foreach($questions as $field => $question)
                                <div class="border-b border-gray-200 pb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $question }}</label>
                                    <div class="flex justify-between items-center space-x-2">
                                        @foreach($emojiData as $value => $data)
                                            <label class="flex-1 text-center cursor-pointer" @click="ratings[{{ $questionIndex }}] = {{ $value }}">
                                                <div class="emoji-option p-2 rounded-lg transition-all duration-200"
                                                     :class="{ 'bg-gray-100': ratings[{{ $questionIndex }}] === {{ $value }} }">
                                                    <div class="text-2xl mb-1">{{ $data['emoji'] }}</div>
                                                    <div class="text-xs text-gray-600">{{ $data['label'] }}</div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="{{ $field }}" x-model="ratings[{{ $questionIndex }}]">
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
                                <textarea id="comments" x-model="comments" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex items-center">
                        <input id="is_anonymous" x-model="isAnonymous" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_anonymous" class="ml-2 block text-sm text-gray-700">
                            Submit feedback anonymously
                        </label>
                    </div>
                </div>
                
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="button" @click="submitFeedback()" :disabled="isSubmitting" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm disabled:opacity-50">
                        <span x-show="!isSubmitting">Submit Feedback</span>
                        <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                    <button type="button" @click="skipFeedback()" :disabled="isSubmitting" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm disabled:opacity-50">
                        <span x-show="!isSubmitting">Skip for Now</span>
                        <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast notification container -->
    <div x-data="{ show: false, message: '', type: 'success' }" 
         x-show="show" 
         x-init="
            window.addEventListener('toast', event => {
                message = event.detail.message;
                type = event.detail.type || 'success';
                show = true;
                setTimeout(() => { show = false; }, 5000);
            });
         "
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed top-4 right-4 z-50 max-w-sm w-full"
         style="display: none;">
        <div x-bind:class="{
            'bg-green-50 border-green-400 text-green-700': type === 'success',
            'bg-blue-50 border-blue-400 text-blue-700': type === 'info',
            'bg-yellow-50 border-yellow-400 text-yellow-700': type === 'warning',
            'bg-red-50 border-red-400 text-red-700': type === 'error'
        }" class="border-l-4 p-4 rounded shadow-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg x-bind:class="{
                        'text-green-400': type === 'success',
                        'text-blue-400': type === 'info',
                        'text-yellow-400': type === 'warning',
                        'text-red-400': type === 'error'
                    }" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p x-text="message" class="text-sm"></p>
                </div>
                <div class="ml-4">
                    <button @click="show = false" class="rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
