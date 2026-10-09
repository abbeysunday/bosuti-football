<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\NewsPost;
use App\Support\RichText;
use Illuminate\Validation\Rule;

class NewsPostRequest extends FormRequest
{
    /** Access is enforced by the `admin` route middleware. */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string', 'max:100000'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_featured_image' => ['boolean'],
            'category' => ['nullable', Rule::in(NewsPost::CATEGORIES)],
            'fixture_id' => ['nullable', 'integer', 'exists:fixtures,id'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['boolean'],
            'is_featured' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published'),
            'is_featured' => $this->boolean('is_featured'),
            'remove_featured_image' => $this->boolean('remove_featured_image'),
        ]);

        // An empty editor still submits markup such as "<div><br></div>".
        if (RichText::isBlank($this->input('content'))) {
            $this->merge(['content' => '']);
        }
    }
}
