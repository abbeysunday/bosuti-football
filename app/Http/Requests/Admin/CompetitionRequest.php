<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Competition;
use Illuminate\Validation\Rule;

class CompetitionRequest extends FormRequest
{
    /** Access is enforced by the `admin` route middleware. */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'season_id' => ['required', 'integer', 'exists:seasons,id'],
            'name' => ['required', 'string', 'max:120'],
            'short_name' => ['nullable', 'string', 'max:30'],
            'type' => ['required', Rule::in(array_keys(Competition::TYPES))],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_logo' => ['boolean'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active'), 'remove_logo' => $this->boolean('remove_logo')]);
    }
}
