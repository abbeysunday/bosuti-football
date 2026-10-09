<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesUploads;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsPostRequest;
use App\Models\Fixture;
use App\Models\NewsPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    use ManagesUploads;

    public function index(Request $request): View
    {
        $state = $request->input('state');

        return view('admin.news.index', [
            'posts' => NewsPost::with('author')
                ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%' . $request->input('q') . '%'))
                ->when($request->filled('category'), fn ($q) => $q->where('category', $request->input('category')))
                ->when($state === 'published', fn ($q) => $q->published())
                ->when($state === 'draft', fn ($q) => $q->where('is_published', false))
                ->latest('updated_at')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create(Request $request): View
    {
        // "Write match report" from a fixture pre-fills the link and category.
        $fixture = $request->filled('fixture') ? Fixture::withTeams()->find($request->integer('fixture')) : null;

        return view('admin.news.form', [
            'post' => new NewsPost([
                'fixture_id' => $fixture?->id,
                'category' => $fixture ? 'Match Report' : null,
                'title' => $fixture ? "Match report: {$fixture->homeTeam->name} {$fixture->scoreline} {$fixture->awayTeam->name}" : null,
            ]),
            'fixtures' => $this->fixtureOptions(),
        ]);
    }

    public function store(NewsPostRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['featured_image', 'remove_featured_image']);
        $data['featured_image'] = $this->handleUpload($request, 'featured_image', 'news');
        $data['author_id'] = $request->user()->id;
        $post = NewsPost::create($data);

        return redirect()->route('admin.news.edit', $post)->with('success', $post->isLive() ? 'Article published.' : 'Article saved as a draft.');
    }

    public function edit(NewsPost $newsPost): View
    {
        return view('admin.news.form', ['post' => $newsPost, 'fixtures' => $this->fixtureOptions()]);
    }

    public function update(NewsPostRequest $request, NewsPost $newsPost): RedirectResponse
    {
        $data = $request->safe()->except(['featured_image', 'remove_featured_image']);
        $data['featured_image'] = $this->handleUpload($request, 'featured_image', 'news', $newsPost->featured_image);
        $newsPost->update($data);

        return redirect()->route('admin.news.edit', $newsPost)->with('success', 'Article updated.');
    }

    public function destroy(NewsPost $newsPost): RedirectResponse
    {
        $newsPost->delete(); // soft delete: the image is kept in case the post is restored

        return redirect()->route('admin.news.index')->with('success', "\"{$newsPost->title}\" deleted.");
    }

    private function fixtureOptions()
    {
        return Fixture::withTeams()->latestFirst()->limit(100)->get()
            ->mapWithKeys(fn (Fixture $f) => [$f->id => $f->match_date->format('d M Y') . ' — ' . $f->homeTeam->name . ' ' . $f->scoreline . ' ' . $f->awayTeam->name]);
    }
}
