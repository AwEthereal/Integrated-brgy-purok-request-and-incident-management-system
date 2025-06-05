@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-blue-600 text-white p-6">
            <h1 class="text-2xl font-bold">{{ $title ?? 'Feedback Form' }}</h1>
            <p class="mt-2">{{ $description ?? 'We value your feedback to help us improve our services.' }}</p>
        </div>

        <form action="{{ route('feedback.store') }}" method="POST" class="p-6" id="feedback-form">
            @csrf
            <input type="hidden" name="item_type" value="{{ $itemType ?? '' }}">
            <input type="hidden" name="item_id" value="{{ $itemId ?? '' }}">
            
            <div class="space-y-6">
                <!-- Service Quality Dimensions -->
                <div class="space-y-4">
                    <h2 class="text-lg font-medium text-gray-900">Please rate your experience:</h2>
                    
                    @php
                        $questions = [
                            'sqd0_rating' => 'Overall Satisfaction',
                            'sqd1_rating' => 'Ease of Use',
                            'sqd2_rating' => 'Speed of Service',
                            'sqd3_rating' => 'Staff Professionalism',
                            'sqd4_rating' => 'Communication'
                        ];
                        
                        $emojiData = [
                            1 => ['emoji' => '😠', 'label' => 'Very Poor'],
                            2 => ['emoji' => '😞', 'label' => 'Poor'],
                            3 => ['emoji' => '😐', 'label' => 'Average'],
                            4 => ['emoji' => '😊', 'label' => 'Good'],
                            5 => ['emoji' => '😍', 'label' => 'Excellent']
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
                    <button type="button" 
                            id="skip-feedback" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Skip for Now
                    </button>
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
        document.getElementById('skip-feedback').addEventListener('click', function(e) {
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
                    @csrf
                    
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

                    @php
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

                    <div class="mt-6">
                        <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">Additional Comments (Optional)</label>
                        <textarea id="comments" name="comments" rows="3" 
                                  class="shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"
                                  placeholder="Please provide any additional feedback or suggestions...">{{ old('comments') }}</textarea>
                    </div>

                    <div class="flex items-center pt-4">
                        <input id="is_anonymous" 
                               name="is_anonymous" 
                               type="checkbox" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                               {{ old('is_anonymous') ? 'checked' : '' }}>
                        <label for="is_anonymous" class="ml-2 block text-sm text-gray-700">
                            Submit feedback anonymously
                        </label>
                    </div>

                    <div class="flex justify-between items-center pt-6">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Back to Dashboard
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setupEmojiSelection() {
        // Handle emoji clicks
        document.querySelectorAll('.emoji-option').forEach(emoji => {
            emoji.addEventListener('click', function() {
                const radio = this.closest('label').querySelector('input[type="radio"]');
                if (!radio) return;
                
                // Get all emojis in the same group
                const groupName = radio.name;
                
                // Remove highlight from all emojis in this group
                document.querySelectorAll(`input[name="${groupName}"]`).forEach(r => {
                    const parentLabel = r.closest('label');
                    if (parentLabel) {
                        const emojiDiv = parentLabel.querySelector('.emoji-option');
                        if (emojiDiv) emojiDiv.classList.remove('bg-gray-100', 'scale-110', 'ring-2', 'ring-blue-300');
                    }
                });
                
                // Highlight the clicked emoji
                radio.checked = true;
                this.classList.add('bg-gray-100', 'scale-110', 'ring-2', 'ring-blue-300');
            });
        });
        
        // Initialize any previously selected emojis
        document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
            const parentLabel = radio.closest('label');
            if (parentLabel) {
                const emojiOption = parentLabel.querySelector('.emoji-option');
                if (emojiOption) emojiOption.classList.add('bg-gray-100', 'scale-110', 'ring-2', 'ring-blue-300');
            }
        });
    }
    
    // Run on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupEmojiSelection);
    } else {
        setupEmojiSelection();
    }
</script>
@endpush

@push('styles')
<style>
    .emoji-option {
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 0.5rem;
    }
    .emoji-option:hover {
        background-color: #f3f4f6;
        transform: scale(1.05);
    }
</style>
@endpush
@endsection
