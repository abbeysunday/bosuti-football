<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\MatchEvent;
use Illuminate\Validation\Rule;

class MatchEventRequest extends FormRequest
{
    /** Access is enforced by the `admin` route middleware. */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $fixture = $this->route('fixture');
        $teamId = $this->integer('team_id');
        $type = $this->input('type');

        // Players must belong to the chosen team, which must be playing in this fixture.
        $teamPlayer = Rule::exists('players', 'id')->where('team_id', $teamId)->whereNull('deleted_at');

        return [
            'type' => ['required', Rule::in(array_keys(MatchEvent::TYPES))],
            'team_id' => ['required', 'integer', Rule::in([$fixture->home_team_id, $fixture->away_team_id])],
            'player_id' => ['required', 'integer', $teamPlayer],
            'related_player_id' => [
                Rule::requiredIf($type === 'substitution'),
                Rule::prohibitedIf(! in_array($type, ['goal', 'penalty_scored', 'substitution'], true)),
                'nullable', 'integer', 'different:player_id', $teamPlayer,
            ],
            'minute' => ['required', 'integer', 'min:0', 'max:130'],
            'additional_minute' => ['nullable', 'integer', 'min:1', 'max:30'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'team_id.in' => 'Choose one of the two teams playing in this match.',
            'player_id.exists' => 'The selected player is not in the chosen team.',
            'related_player_id.exists' => 'The second player must be in the same team.',
            'related_player_id.required' => 'Choose the player coming on.',
            'related_player_id.different' => 'The two players must be different.',
            'related_player_id.prohibited' => 'Only goals and substitutions take a second player.',
        ];
    }

    public function attributes(): array
    {
        return ['player_id' => 'player', 'related_player_id' => 'second player', 'team_id' => 'team'];
    }
}
