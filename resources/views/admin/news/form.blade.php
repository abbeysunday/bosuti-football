<x-layouts.admin :title="$post->exists ? 'Edit article' : 'New article'">
    @push('head')
        @vite('resources/js/editor.js')
    @endpush

    <x-admin.page-header :title="$post->exists ? 'Edit article' : 'New article'" :back="route('admin.news.index')">
        @if ($post->exists && $post->isLive())
            <x-slot name="actions"><a href="{{ route('news.show', $post) }}" class="btn btn-ghost" target="_blank" rel="noopener">View article</a></x-slot>
        @endif
    </x-admin.page-header>

    <form method="POST" action="{{ $post->exists ? route('admin.news.update', $post) : route('admin.news.store') }}" enctype="multipart/form-data" class="grid gap-6 xl:grid-cols-[1fr_22rem]">
        @csrf
        @if ($post->exists) @method('PUT') @endif

        <div class="space-y-6">
            <x-admin.section title="Content">
                <div class="grid gap-5">
                    <x-admin.field name="title" label="Title" :value="$post->title" required />
                    <x-admin.field name="excerpt" label="Summary" type="textarea" rows="2" :value="$post->excerpt" maxlength="500" help="One or two sentences shown on news cards. Leave empty to use the start of the article." />
                    {{-- Rich-text editor (Trix). The HTML is sanitised on the server before it is saved. --}}
                    <div class="min-w-0">
                        <label for="content-editor" id="content-label" class="form-label">Article<span class="ms-0.5 text-gold-light" aria-hidden="true">*</span></label>
                        <input id="content" type="hidden" name="content" value="{{ old('content', $post->exists ? $post->body_html : '') }}">
                        <trix-editor id="content-editor" input="content" @class(['trix-content form-input', 'is-invalid' => $errors->has('content')])
                            aria-labelledby="content-label" aria-describedby="content-help{{ $errors->has('content') ? ' content-error' : '' }}"></trix-editor>
                        <p id="content-help" class="mt-1.5 text-xs text-ink-muted">Use the toolbar for headings, bold, quotes, lists and links. Add pictures as the featured image.</p>
                        @error('content')
                            <p id="content-error" class="form-error" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-admin.section>
        </div>

        <div class="space-y-6">
            <x-admin.section title="Publishing">
                <div class="grid gap-4">
                    <x-admin.field name="is_published" label="Published" type="checkbox" :value="$post->is_published" help="Unticked articles stay as drafts." />
                    <x-admin.field name="published_at" label="Publish date" type="datetime-local" :value="$post->published_at?->format('Y-m-d\TH:i')" help="Leave empty to publish now. A future date schedules it." />
                    <x-admin.field name="is_featured" label="Featured" type="checkbox" :value="$post->is_featured" />
                </div>
            </x-admin.section>

            <x-admin.section title="Details">
                <div class="grid gap-4">
                    <x-admin.field name="category" label="Category" type="select" :options="array_combine(\App\Models\NewsPost::CATEGORIES, \App\Models\NewsPost::CATEGORIES)" :value="$post->category" empty="No category" />
                    <x-admin.field name="fixture_id" label="Related match" type="select" :options="$fixtures" :value="$post->fixture_id" empty="None" help="Links a match report to its fixture page." />
                    <x-admin.field name="featured_image" label="Featured image" type="file" accept="image/png,image/jpeg,image/webp" :preview="$post->image_url" removable help="Landscape image, up to 4 MB." />
                </div>
            </x-admin.section>

            <x-admin.form-actions :cancel="route('admin.news.index')" :label="$post->exists ? 'Save article' : 'Save article'" />
        </div>
    </form>
</x-layouts.admin>
