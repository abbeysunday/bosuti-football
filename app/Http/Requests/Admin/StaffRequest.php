<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Staff;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
{
    /** Access is enforced by the `admin` route middleware. */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'role' => ['required', 'string', 'max:80'],
            'type' => ['required', Rule::in(array_keys(Staff::TYPES))],
            'team_id' => ['nullable', 'integer', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_photo' => ['boolean'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:-1000', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'remove_photo' => $this->boolean('remove_photo'),
            'sort_order' => (int) $this->input('sort_order', 0),
        ]);
    }
}
