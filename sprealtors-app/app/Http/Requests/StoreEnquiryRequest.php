<?php

namespace App\Http\Requests;

use App\Models\Enquiry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'email' => ['nullable', 'email', 'max:180'],
            'message' => ['nullable', 'string', 'max:2000'],
            'property_id' => ['nullable', 'integer', 'exists:properties,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'source' => ['required', Rule::in(array_keys(Enquiry::sourceOptions()))],
            // Honeypot: real users never fill this in.
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Please enter a valid phone number.',
            'website.prohibited' => 'Your submission could not be processed.',
        ];
    }
}
