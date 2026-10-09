<x-layouts.admin :title="$video->exists ? 'Edit video' : 'Add video'">
    <x-admin.page-header :title="$video->exists ? 'Edit video' : 'Add video'" :back="route('admin.videos.index')" />

    <form method="POST" action="{{ $video->exists ? route('admin.videos.update', $video) : route('admin.videos.store') }}" enctype="multipart/form-data" class="card max-w-3xl p-5 sm:p-8">
        @csrf
        @if ($video->exists) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <x-admin.field name="title" label="Title" :value="$video->title" required class="sm:col-span-2" />
            <x-admin.field name="video_url" label="Video link" type="url" :value="$video->video_url" placeholder="https://www.youtube.com/watch?v=…" required inputmode="url" class="sm:col-span-2" />
            <x-admin.field name="category" label="Category" type="select" :options="array_combine(\App\Models\Video::CATEGORIES, \App\Models\Video::CATEGORIES)" :value="$video->category" empty="No category" />
            <x-admin.field name="published_at" label="Publish date" type="datetime-local" :value="$video->published_at?->format('Y-m-d\TH:i')" help="Leave empty to show immediately." />
            <x-admin.field name="thumbnail" label="Custom thumbnail" type="file" accept="image/png,image/jpeg,image/webp" :preview="$video->thumbnail ? $video->thumbnail_url : null" removable help="Optional for YouTube links." class="sm:col-span-2" />
            <x-admin.field name="description" label="Description" type="textarea" rows="3" :value="$video->description" class="sm:col-span-2" />
            <x-admin.field name="is_featured" label="Featured" type="checkbox" :value="$video->is_featured" class="sm:col-span-2" />
        </div>

        <x-admin.form-actions :cancel="route('admin.videos.index')" :label="$video->exists ? 'Save video' : 'Add video'" />
    </form>
</x-layouts.admin>
