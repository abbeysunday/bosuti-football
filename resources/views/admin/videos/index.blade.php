<x-layouts.admin title="Videos">
    <x-admin.page-header title="Videos" description="Highlights and interviews. YouTube links get a thumbnail automatically.">
        <x-slot name="actions"><a href="{{ route('admin.videos.create') }}" class="btn btn-primary">Add video</a></x-slot>
    </x-admin.page-header>

    <x-admin.filters :action="route('admin.videos.index')" placeholder="Search videos" />

    @if ($videos->isEmpty())
        <x-empty-state title="No videos yet" description="Paste a YouTube link to add match highlights.">
            <x-slot name="action"><a href="{{ route('admin.videos.create') }}" class="btn btn-primary">Add video</a></x-slot>
        </x-empty-state>
    @else
        <x-admin.table label="Videos">
            <x-slot name="head">
                <th scope="col" class="px-4 py-3">Video</th>
                <th scope="col" class="px-4 py-3">Category</th>
                <th scope="col" class="px-4 py-3">Platform</th>
                <th scope="col" class="px-4 py-3">Published</th>
                <th scope="col" class="px-4 py-3 text-end"><span class="sr-only">Actions</span></th>
            </x-slot>
            @foreach ($videos as $video)
                <tr>
                    <td class="px-4 py-3">
                        <span class="flex items-center gap-3">
                            @if ($video->thumbnail_url)<img src="{{ $video->thumbnail_url }}" alt="" class="h-10 w-16 shrink-0 rounded-md object-cover">@endif
                            <span class="font-semibold text-white">{{ $video->title }}@if ($video->is_featured)<x-admin.badge color="gold" class="ms-1">Featured</x-admin.badge>@endif</span>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-ink-2">{{ $video->category ?? '—' }}</td>
                    <td class="px-4 py-3 text-ink-2">{{ ucfirst($video->platform ?? 'other') }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-ink-2">{{ $video->published_at?->format('d M Y') ?? 'Immediately' }}</td>
                    <td class="px-4 py-2">
                        <x-admin.row-actions :name="$video->title" :show="$video->video_url" :edit="route('admin.videos.edit', $video)" :delete="route('admin.videos.destroy', $video)" />
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
        {{ $videos->links() }}
    @endif
</x-layouts.admin>
