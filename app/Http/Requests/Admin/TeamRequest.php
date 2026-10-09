<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeamRequest extends FormRequest
{
    /** Access is enforced by the `admin` route middleware. */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', Rule::unique('teams', 'name')->ignore($this->route('team'))],
            'short_name' => ['nullable', 'string', 'max:12'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_logo' => ['boolean'],
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => ['nullable', 'string', 'max:2000'],
            'founded_year' => ['nullable', 'integer', 'min:1950', 'max:' . date('Y')],
            'captain_name' => ['nullable', 'string', 'max:80'],
            'coach_name' => ['nullable', 'string', 'max:80'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return ['primary_color.regex' => 'Use a hex colour such as #009A56.', 'secondary_color.regex' => 'Use a hex colour such as #D9AD45.'];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active'), 'remove_logo' => $this->boolean('remove_logo')]);
    }
}
