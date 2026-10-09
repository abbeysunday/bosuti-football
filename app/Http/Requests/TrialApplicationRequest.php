<?php

namespace App\Http\Requests;

use App\Models\Player;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Public "Join the team" trial application form. */
class TrialApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\s-]{7,30}$/'],
            'matric_number' => ['required', 'string', 'max:40'],
            'department' => ['nullable', 'string', 'max:120'],
            'level' => ['required', Rule::in(Player::LEVELS)],
            'position' => ['required', Rule::in(array_keys(Player::POSITIONS))],
            'dominant_foot' => ['nullable', Rule::in(array_keys(Player::FEET))],
            'height' => ['nullable', 'string', 'max:20'],
            'previous_team' => ['nullable', 'string', 'max:120'],
            'experience' => ['nullable', 'string', 'max:2000'],
            'website' => ['prohibited'], // honeypot: real visitors never see this field
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => 'Enter a valid phone number.'];
    }

    public function attributes(): array
    {
        return ['position' => 'preferred position', 'experience' => 'playing experience'];
    }

    /** Validated input mapped onto trial_applications columns. */
    public function applicationData(): array
    {
        $data = $this->safe()->except(['position', 'experience', 'website']);

        return $data + [
            'preferred_position' => $this->validated('position'),
            'playing_experience' => $this->validated('experience'),
        ];
    }
}
