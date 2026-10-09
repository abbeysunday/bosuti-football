<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Player;
use Illuminate\Validation\Rule;

class PlayerRequest extends FormRequest
{
    /** Access is enforced by the `admin` route middleware. */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $player = $this->route('player');

        return [
            'team_id' => ['required', 'integer', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_photo' => ['boolean'],
            // A shirt number is unique within the same team (ignoring removed players).
            'jersey_number' => [
                'nullable', 'integer', 'min:1', 'max:99',
                Rule::unique('players', 'jersey_number')
                    ->where('team_id', $this->integer('team_id'))
                    ->whereNull('deleted_at')
                    ->ignore($player),
            ],
            'position' => ['required', Rule::in(array_keys(Player::POSITIONS))],
            'department' => ['nullable', 'string', 'max:120'],
            'level' => ['nullable', 'string', 'max:20'],
            'matric_number' => ['nullable', 'string', 'max:40', Rule::unique('players', 'matric_number')->whereNull('deleted_at')->ignore($player)],
            'state_of_origin' => ['nullable', 'string', 'max:60'],
            'dominant_foot' => ['nullable', Rule::in(array_keys(Player::FEET))],
            'height' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:3000'],
            'is_captain' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'jersey_number.unique' => 'Another player in this team already wears this number.',
            'matric_number.unique' => 'A player with this matric number already exists.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_captain' => $this->boolean('is_captain'),
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active'),
            'remove_photo' => $this->boolean('remove_photo'),
        ]);
    }
}
