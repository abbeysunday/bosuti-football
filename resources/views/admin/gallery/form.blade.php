<x-layouts.admin :title="$item->exists ? 'Edit photo' : 'Upload photos'">
    <x-admin.page-header :title="$item->exists ? 'Edit photo' : 'Upload photos'" :back="route('admin.gallery.index')" />

    <form method="POST" action="{{ $item->exists ? route('admin.gallery.update', $item) : route('admin.gallery.store') }}" enctype="multipart/form-data" class="card max-w-3xl p-5 sm:p-8">
        @csrf
        @if ($item->exists) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            @if ($item->exists)
                <x-admin.field name="image" label="Replace photo" type="file" accept="image/png,image/jpeg,image/webp" :preview="$item->image_url" help="JPG, PNG or WebP, up to 5 MB." class="sm:col-span-2" />
            @else
                <x-admin.field name="images[]" label="Photos" type="file" accept="image/png,image/jpeg,image/webp" multiple required help="Select up to 20 photos (JPG, PNG or WebP, 5 MB each). The details below apply to all of them." class="sm:col-span-2" />
            @endif
            <x-admin.field name="title" label="Title / caption" :value="$item->title" class="sm:col-span-2" />
            <x-admin.field name="category" label="Category" type="select" :options="\App\Models\GalleryItem::CATEGORIES" :value="$item->category" empty="No category" />
            <x-admin.field name="sort_order" label="Sort order" type="number" :value="$item->sort_order ?? 0" inputmode="numeric" help="Lower numbers appear first." />
            <x-admin.field name="fixture_id" label="Match" type="select" :options="$fixtures" :value="$item->fixture_id" empty="Not linked to a match" />
            <x-admin.field name="team_id" label="Team" type="select" :options="$teams" :value="$item->team_id" empty="Not linked to a team" />
            <x-admin.field name="description" label="Description" type="textarea" rows="3" :value="$item->description" class="sm:col-span-2" />
            <x-admin.field name="is_featured" label="Featured" type="checkbox" :value="$item->is_featured" help="Featured photos appear on the homepage." class="sm:col-span-2" />
        </div>

        <x-admin.form-actions :cancel="route('admin.gallery.index')" :label="$item->exists ? 'Save photo' : 'Upload'" :loading="$item->exists ? 'Saving…' : 'Uploading…'" />
    </form>
</x-layouts.admin>
