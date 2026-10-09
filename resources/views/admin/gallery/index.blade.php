<x-layouts.admin title="Gallery">
    <x-admin.page-header title="Gallery" description="Photos from matches, training and fans. Lower sort numbers appear first.">
        <x-slot name="actions"><a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">Upload photos</a></x-slot>
    </x-admin.page-header>

    <x-admin.filters :action="route('admin.gallery.index')" :search="false">
        <x-admin.select-filter name="category" label="Category" :options="\App\Models\GalleryItem::CATEGORIES" all="All categories" />
    </x-admin.filters>

    @if ($items->isEmpty())
        <x-empty-state title="No photos yet" description="Upload match-day, training and fan photos. You can select several at once.">
            <x-slot name="action"><a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">Upload photos</a></x-slot>
        </x-empty-state>
    @else
        <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($items as $item)
                <li class="card overflow-hidden">
                    <img src="{{ $item->image_url }}" alt="{{ $item->alt_text }}" class="aspect-[4/3] w-full bg-pitch-900 object-cover" loading="lazy">
                    <div class="flex items-start gap-2 p-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-white">{{ $item->title ?? 'Untitled' }}</p>
                            <p class="truncate text-xs text-ink-muted">{{ $item->category_label ?? 'No category' }}{{ $item->is_featured ? ' · Featured' : '' }}</p>
                        </div>
                        <x-admin.row-actions :name="$item->title ?? 'this photo'" :edit="route('admin.gallery.edit', $item)" :delete="route('admin.gallery.destroy', $item)" />
                    </div>
                </li>
            @endforeach
        </ul>
        {{ $items->links() }}
    @endif
</x-layouts.admin>
