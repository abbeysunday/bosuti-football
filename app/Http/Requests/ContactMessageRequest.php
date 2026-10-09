<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Public contact form. */
class ContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+()\s-]{7,30}$/'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['prohibited'], // honeypot: real visitors never see this field
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a valid phone number.',
            'message.min' => 'Please write a little more so we can help (at least 10 characters).',
        ];
    }
}
