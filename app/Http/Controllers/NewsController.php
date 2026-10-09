<?php

namespace App\Http\Controllers;

use App\Models\NewsPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $category = in_array($request->input('category'), NewsPost::CATEGORIES, true) ? $request->input('category') : null;

        return view('pages.news', [
            'posts' => NewsPost::published()
                ->when($category, fn ($q) => $q->where('category', $category))
                ->newest()
                ->paginate(9)
                ->withQueryString(),
            'categories' => NewsPost::published()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
            'currentCategory' => $category,
        ]);
    }

    public function show(NewsPost $newsPost): View
    {
        // Drafts and scheduled posts are not public.
        abort_unless($newsPost->isLive(), 404);

        $newsPost->load(['author', 'fixture.homeTeam', 'fixture.awayTeam']);

        return view('pages.article', [
            'post' => $newsPost,
            'related' => NewsPost::published()->whereKeyNot($newsPost->id)
                ->when($newsPost->category, fn ($q) => $q->orderByRaw('category = ? DESC', [$newsPost->category]))
                ->newest()->limit(3)->get(),
        ]);
    }
}
