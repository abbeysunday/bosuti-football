<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\GalleryItem;
use Illuminate\Validation\Rule;

class GalleryItemRequest extends FormRequest
{
    /** Access is enforced by the `admin` route middleware. */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $creating = $this->isMethod('post');

        return [
            // Creating accepts several photos at once; editing replaces a single photo.
            'images' => [$creating ? 'required' : 'prohibited', 'array', 'max:20'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'title' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', Rule::in(array_keys(GalleryItem::CATEGORIES))],
            'fixture_id' => ['nullable', 'integer', 'exists:fixtures,id'],
            'team_id' => ['nullable', 'integer', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:-1000', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return ['images.required' => 'Choose at least one photo to upload.', 'images.*.max' => 'Each photo must be 5 MB or smaller.'];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_featured' => $this->boolean('is_featured'), 'sort_order' => (int) $this->input('sort_order', 0)]);
    }
}
