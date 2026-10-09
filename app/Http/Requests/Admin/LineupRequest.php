<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LineupRequest extends FormRequest
{
    /** Access is enforced by the `admin` route middleware. */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $teamPlayer = Rule::exists('players', 'id')->where('team_id', $this->route('team')->id)->whereNull('deleted_at');

        return [
            'players' => ['array'],
            'players.*' => ['integer', 'distinct', $teamPlayer],
            'starting' => ['array', 'max:11'],
            'starting.*' => ['integer', 'distinct', 'in_array:players.*'],
            'captain_id' => ['nullable', 'integer', 'in_array:players.*'],
        ];
    }

    public function messages(): array
    {
        return [
            'starting.max' => 'A starting line-up can have at most 11 players.',
            'starting.*.in_array' => 'Starters must also be ticked as in the squad.',
            'players.*.exists' => 'Only players from this team can be named in its line-up.',
        ];
    }
}
