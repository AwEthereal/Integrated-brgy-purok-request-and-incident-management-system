<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\IncidentReport;

class PublicIncidentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $input = $this->all();
        $sanitize = function ($v) { return is_string($v) ? trim(strip_tags($v)) : $v; };
        $input['reporter_name'] = $sanitize($input['reporter_name'] ?? null);
        $input['email'] = $sanitize($input['email'] ?? null);
        $input['description'] = $sanitize($input['description'] ?? null);
        $input['location'] = $sanitize($input['location'] ?? null);
        $input['incident_type'] = $sanitize($input['incident_type'] ?? null);
        $input['incident_type_other'] = $sanitize($input['incident_type_other'] ?? null);
        $input['contact_number'] = preg_replace('/[^0-9]/', '', (string)($input['contact_number'] ?? ''));
        $this->replace($input);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reporter_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20', 'regex:/^[0-9]{10,15}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:255'],
            'incident_type' => ['nullable', 'in:' . implode(',', array_keys(IncidentReport::TYPES))],
            'incident_type_other' => ['nullable', 'string', 'max:100'],
            'photo_data' => ['nullable', 'string'],
            'photos_data' => ['nullable', 'string'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
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
            $limit = (int) config('rate_limits.public_incident_contact_daily', 5);
            $count = IncidentReport::where('contact_number', $phone)
                ->where('created_at', '>=', now()->subDay())
                ->count();
            if ($count >= $limit) {
                $validator->errors()->add('contact_number', 'You have reached the daily submission limit for incident reports. Please try again later.');
            }
        });
    }
}
