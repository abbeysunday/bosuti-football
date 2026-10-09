<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Video;
use Illuminate\Validation\Rule;

class VideoRequest extends FormRequest
{
    /** Access is enforced by the `admin` route middleware. */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'video_url' => ['required', 'url:http,https', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_thumbnail' => ['boolean'],
            'category' => ['nullable', Rule::in(Video::CATEGORIES)],
            'description' => ['nullable', 'string', 'max:2000'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_featured' => $this->boolean('is_featured'), 'remove_thumbnail' => $this->boolean('remove_thumbnail')]);
    }
}
