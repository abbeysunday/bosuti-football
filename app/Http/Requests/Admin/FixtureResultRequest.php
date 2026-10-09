<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Fixture;
use Illuminate\Validation\Rule;

class FixtureResultRequest extends FormRequest
{
    /** Access is enforced by the `admin` route middleware. */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $scored = Rule::requiredIf(in_array($this->input('status'), Fixture::SCORED_STATUSES, true));

        return [
            'status' => ['required', Rule::in(array_keys(Fixture::STATUSES))],
            'home_score' => [$scored, 'nullable', 'integer', 'min:0', 'max:99'],
            'away_score' => [$scored, 'nullable', 'integer', 'min:0', 'max:99'],
            'referee' => ['nullable', 'string', 'max:80'],
            'attendance' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'report' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'home_score.required' => 'Enter the home score to save a live or completed result.',
            'away_score.required' => 'Enter the away score to save a live or completed result.',
        ];
    }
}
