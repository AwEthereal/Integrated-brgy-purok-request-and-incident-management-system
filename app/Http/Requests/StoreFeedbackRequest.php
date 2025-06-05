<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\IncidentReport;

class StoreFeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow feedback if the user owns the incident report
        $incidentReport = IncidentReport::findOrFail($this->route('incident_report'));
        return $incidentReport->user_id === auth()->id() && 
               !$incidentReport->hasFeedback() &&
               in_array($incidentReport->status, ['Resolved', 'Rejected']);
    }
    
    protected function prepareForValidation()
    {
        $this->merge([
            'is_anonymous' => $this->has('is_anonymous'),
            // Convert all SQD ratings to integers
            'sqd0_rating' => (int) ($this->sqd0_rating ?? 0),
            'sqd1_rating' => (int) ($this->sqd1_rating ?? 0),
            'sqd2_rating' => (int) ($this->sqd2_rating ?? 0),
            'sqd3_rating' => (int) ($this->sqd3_rating ?? 0),
            'sqd4_rating' => (int) ($this->sqd4_rating ?? 0),
            'sqd5_rating' => (int) ($this->sqd5_rating ?? 0),
            'sqd6_rating' => (int) ($this->sqd6_rating ?? 0),
            'sqd7_rating' => (int) ($this->sqd7_rating ?? 0),
            'sqd8_rating' => (int) ($this->sqd8_rating ?? 0),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // All SQD ratings are required and must be between 1 and 5
            'sqdo_rating' => 'required|integer|min:1|max:5',
            'sqd1_rating' => 'required|integer|min:1|max:5',
            'sqd2_rating' => 'required|integer|min:1|max:5',
            'sqd3_rating' => 'required|integer|min:1|max:5',
            'sqd4_rating' => 'required|integer|min:1|max:5',
            'sqd5_rating' => 'required|integer|min:1|max:5',
            'sqd6_rating' => 'required|integer|min:1|max:5',
            'sqd7_rating' => 'required|integer|min:1|max:5',
            'sqd8_rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
            'is_anonymous' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'sqdo_rating.required' => 'Please rate your overall satisfaction.',
            'sqd1_rating.required' => 'Please rate the time spent for your transaction.',
            'sqd2_rating.required' => 'Please rate the accuracy of information provided.',
            'sqd3_rating.required' => 'Please rate the simplicity and convenience of the process.',
            'sqd4_rating.required' => 'Please rate how easily you found the information.',
            'sqd5_rating.required' => 'Please rate the acceptability of fees for your transaction.',
            'sqd6_rating.required' => 'Please rate your confidence in the security of the transaction.',
            'sqd7_rating.required' => 'Please rate the responsiveness of online support.',
            'sqd8_rating.required' => 'Please rate if you got what you needed from the office.',
            '*.integer' => 'Please select a valid rating.',
            '*.min' => 'Please select a rating between 1 and 5.',
            '*.max' => 'Please select a rating between 1 and 5.',
            'comments.max' => 'Your comments may not be greater than 1000 characters.',
        ];
    }
}
