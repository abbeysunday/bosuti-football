<x-layouts.admin title="News">
    <x-admin.page-header title="News" description="Club news, match reports and announcements.">
        <x-slot name="actions"><a href="{{ route('admin.news.create') }}" class="btn btn-primary">New article</a></x-slot>
    </x-admin.page-header>

    <x-admin.filters :action="route('admin.news.index')" placeholder="Search titles">
        <x-admin.select-filter name="category" label="Category" :options="array_combine(\App\Models\NewsPost::CATEGORIES, \App\Models\NewsPost::CATEGORIES)" all="All categories" />
        <x-admin.select-filter name="state" label="Status" :options="['published' => 'Published', 'draft' => 'Drafts']" all="All statuses" />
    </x-admin.filters>

    @if ($posts->isEmpty())
        <x-empty-state title="No articles found" description="Publish news and match reports to keep supporters informed.">
            <x-slot name="action"><a href="{{ route('admin.news.create') }}" class="btn btn-primary">Write an article</a></x-slot>
        </x-empty-state>
    @else
        <x-admin.table label="News articles">
            <x-slot name="head">
                <th scope="col" class="px-4 py-3">Article</th>
                <th scope="col" class="px-4 py-3">Category</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-4 py-3">Published</th>
                <th scope="col" class="px-4 py-3 text-end"><span class="sr-only">Actions</span></th>
            </x-slot>
            @foreach ($posts as $post)
                <tr>
                    <td class="px-4 py-3">
                        <span class="flex items-center gap-3">
                            @if ($post->image_url)
                                <img src="{{ $post->image_url }}" alt="" class="h-10 w-14 shrink-0 rounded-md object-cover">
                            @endif
                            <span class="min-w-0">
                                <span class="line-clamp-2 font-semibold text-white">{{ $post->title }}</span>
                                <span class="text-xs text-ink-muted">{{ $post->author?->name ?? 'Unknown author' }}</span>
                            </span>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-ink-2">{{ $post->category ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($post->isLive())
                            <x-admin.badge color="green">Published</x-admin.badge>
                        @elseif ($post->is_published)
                            <x-admin.badge color="blue">Scheduled</x-admin.badge>
                        @else
                            <x-admin.badge>Draft</x-admin.badge>
                        @endif
                        @if ($post->is_featured)<x-admin.badge color="gold" class="ms-1">Featured</x-admin.badge>@endif
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-ink-2">{{ $post->published_at?->format('d M Y') ?? '—' }}</td>
                    <td class="px-4 py-2">
                        <x-admin.row-actions :name="$post->title" :show="$post->isLive() ? route('news.show', $post) : null" :edit="route('admin.news.edit', $post)" :delete="route('admin.news.destroy', $post)" confirm="It will be removed from the website." />
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
        {{ $posts->links() }}
    @endif
</x-layouts.admin>
