<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'contact_number' => [
                'required', 
                'string', 
                'regex:/^[0-9]{11}$/',
                Rule::unique(User::class)->ignore($this->user()->id, 'id'),
            ],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'civil_status' => ['nullable', 'in:single,married,widowed,separated,divorced'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'purok_id' => ['required', 'exists:puroks,id'],
        ];
    }
    
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Combine first, middle, and last name into the name field
        $this->merge([
            'name' => trim(implode(' ', array_filter([
                $this->first_name,
                $this->middle_name,
                $this->last_name
            ])))
        ]);
        
        // Format contact number to remove any non-numeric characters
        if ($this->has('contact_number')) {
            $this->merge([
                'contact_number' => preg_replace('/[^0-9]/', '', $this->contact_number)
            ]);
        }
        
        // Combine first and last name for the name field
        if ($this->has('first_name') && $this->has('last_name')) {
            $this->merge([
                'name' => trim(sprintf('%s %s', $this->first_name, $this->last_name))
            ]);
        }
    }
}
