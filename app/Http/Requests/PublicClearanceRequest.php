<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Request as ServiceRequest;

class PublicClearanceRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $input = $this->all();
        $sanitize = function ($v) {
            return is_string($v) ? trim(strip_tags($v)) : $v;
        };
        $input['purpose'] = $sanitize($input['purpose'] ?? null);
        $input['requester_name'] = $sanitize($input['requester_name'] ?? null);
        $input['email'] = $sanitize($input['email'] ?? null);
        $input['contact_number'] = preg_replace('/[^0-9]/', '', (string)($input['contact_number'] ?? ''));
        $input['gender'] = $sanitize($input['gender'] ?? null);
        $input['address'] = $sanitize($input['address'] ?? null);
        // Remove trailing street suffix to avoid duplication in PDF (we append 'St.,')
        if (!empty($input['address']) && is_string($input['address'])) {
            $addr = $input['address'];
            // Trim trailing occurrences of 'street', 'st', or 'st.' (case-insensitive)
            $addr = preg_replace('/\b(street|st\.?)(\s*)$/i', '', $addr);
            // Collapse extra spaces
            $addr = trim(preg_replace('/\s{2,}/', ' ', $addr));
            $input['address'] = $addr;
        }
        // Age is optional and not persisted; sanitize lightly if provided
        if (isset($input['age'])) {
            $age = is_numeric($input['age']) ? (int) $input['age'] : null;
            $input['age'] = $age;
        }
        $this->replace($input);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purpose' => ['required', 'string', 'max:500'],
            'requester_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20', 'regex:/^[0-9]{10,15}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'purok_id' => ['required', 'integer', 'exists:puroks,id'],
            'gender' => ['nullable', 'in:Male,Female'],
            'address' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'between:1,120'],
            // Either valid ID (front/back) OR live face photo
            'valid_id_front' => ['required_without:face_photo', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:4096'],
            'valid_id_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:4096'],
            'face_photo' => ['required_without:valid_id_front', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!config('rate_limits.enable_contact_limits', true)) {
                return;
            }
            $phone = $this->input('contact_number');
            if (!$phone) {
                return;
            }
            $limit = (int) config('rate_limits.public_contact_daily', 3);
            $count = ServiceRequest::where('contact_number', $phone)
                ->where('created_at', '>=', now()->subDay())
                ->count();
            if ($count >= $limit) {
                $validator->errors()->add('contact_number', 'You have reached the daily submission limit. Please try again later.');
            }
        });
    }
}
