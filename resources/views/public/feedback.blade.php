<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <title>Public Feedback</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
<div class="max-w-4xl mx-auto px-4 md:px-6 py-4 sm:py-6 pb-32 sm:pb-6">
    <div class="flex items-center justify-between mb-4 sm:mb-6">
        <a href="{{ url('/public') }}"
           class="inline-flex items-center px-3 sm:px-4 py-2 bg-white hover:bg-gray-50 rounded-xl shadow text-sm font-semibold text-gray-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
        <div class="hidden sm:block text-sm text-gray-500">Public Services</div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-blue-600 text-white p-4 sm:p-6">
            <h1 class="text-xl sm:text-2xl font-bold">General Feedback</h1>
            <p class="mt-2 text-sm sm:text-base text-blue-100">Please share your feedback about our services.</p>
        </div>

        <form action="{{ route('public.feedback.store') }}" method="POST" class="p-4 sm:p-6" id="public-feedback-form">
            @csrf
            <input type="hidden" name="item_type" value="general">
            <input type="hidden" name="item_id" value="">
            <input type="hidden" name="is_public" value="1">

            <div class="space-y-4 sm:space-y-6">
                <div class="space-y-3 sm:space-y-4">
                    <h2 class="text-base sm:text-lg font-medium text-gray-900">Please rate your experience:</h2>

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
                        <div class="border-b border-gray-200 pb-4 sm:pb-6">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2 sm:mb-3">{{ $question }}</label>
                            <div class="grid grid-cols-5 gap-1 sm:gap-2">
                                @foreach($emojiData as $value => $data)
                                    <label class="flex-1 text-center cursor-pointer">
                                        <input type="radio"
                                               name="{{ $field }}"
                                               value="{{ $value }}"
                                               class="peer sr-only"
                                               {{ old($field) == $value ? 'checked' : '' }}
                                               required>
                                        <div class="emoji-option p-1.5 sm:p-2 rounded-lg transition-all duration-200 hover:bg-gray-100 active:scale-95 peer-checked:bg-blue-50 peer-checked:ring-2 peer-checked:ring-blue-500 peer-checked:ring-offset-1">
                                            <div class="text-2xl sm:text-3xl leading-none mb-1">{{ $data['emoji'] }}</div>
                                            <div class="text-[9px] sm:text-xs leading-tight text-gray-600">{{ $data['label'] }}</div>
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

                <div>
                    <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">Additional Comments (Optional)</label>
                    <textarea id="comments"
                              name="comments"
                              rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                              placeholder="Please share any additional comments or suggestions...">{{ old('comments') }}</textarea>
                </div>

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

                <div class="hidden sm:flex justify-end items-center pt-6">
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Submit Feedback
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="fixed inset-x-0 bottom-0 z-50 sm:hidden">
    <div class="max-w-4xl mx-auto px-4 pb-4">
        <div class="bg-white/90 backdrop-blur border border-gray-200 shadow-xl rounded-2xl p-3">
            <button form="public-feedback-form" type="submit"
                    class="inline-flex w-full items-center justify-center px-4 py-3 rounded-xl bg-blue-600 text-white font-semibold shadow-lg">
                Submit Feedback
            </button>
        </div>
    </div>
</div>
</body>
</html>
