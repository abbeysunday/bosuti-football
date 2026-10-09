<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Fixture;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FixtureRequest extends FormRequest
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
            'competition_id' => ['required', 'integer', 'exists:competitions,id'],
            'home_team_id' => ['required', 'integer', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'away_team_id' => ['required', 'integer', 'different:home_team_id', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'match_date' => ['required', 'date'],
            'kickoff_time' => ['nullable', 'date_format:H:i'],
            'venue' => ['nullable', 'string', 'max:120'],
            'matchday' => ['nullable', 'integer', 'min:1', 'max:200'],
            'status' => ['required', Rule::in(array_keys(Fixture::STATUSES))],
            'home_score' => [$scored, 'nullable', 'integer', 'min:0', 'max:99'],
            'away_score' => [$scored, 'nullable', 'integer', 'min:0', 'max:99'],
            'referee' => ['nullable', 'string', 'max:80'],
            'attendance' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'featured' => ['boolean'],
            'report' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /** A team cannot be scheduled for two active fixtures on the same day. */
    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->isNotEmpty() || in_array($this->input('status'), Fixture::INACTIVE_STATUSES, true)) {
                return;
            }

            $teams = [(int) $this->input('home_team_id'), (int) $this->input('away_team_id')];
            $clash = Fixture::query()
                ->whereDate('match_date', $this->input('match_date'))
                ->whereNotIn('status', Fixture::INACTIVE_STATUSES)
                ->where(fn ($q) => $q->whereIn('home_team_id', $teams)->orWhereIn('away_team_id', $teams))
                ->when($this->route('fixture'), fn ($q, $fixture) => $q->whereKeyNot($fixture->getKey()))
                ->with('homeTeam', 'awayTeam')
                ->first();

            if ($clash) {
                $validator->errors()->add('match_date', "Schedule conflict: {$clash->homeTeam?->name} v {$clash->awayTeam?->name} is already on this date. A team can only play one match per day.");
            }
        }];
    }

    public function messages(): array
    {
        return [
            'away_team_id.different' => 'A team cannot play against itself. Choose a different away team.',
            'home_score.required' => 'Enter the home score for a live or completed match.',
            'away_score.required' => 'Enter the away score for a live or completed match.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'featured' => $this->boolean('featured'),
            'kickoff_time' => $this->filled('kickoff_time') ? substr((string) $this->input('kickoff_time'), 0, 5) : null,
        ]);
    }
}
