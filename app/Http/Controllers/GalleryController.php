<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $category = array_key_exists((string) $request->input('category'), GalleryItem::CATEGORIES) ? $request->input('category') : null;

        return view('pages.gallery', [
            'items' => GalleryItem::with(['fixture.homeTeam', 'fixture.awayTeam'])
                ->when($category, fn ($q) => $q->where('category', $category))
                ->ordered()
                ->paginate(16)
                ->withQueryString(),
            'categories' => GalleryItem::CATEGORIES,
            'currentCategory' => $category,
        ]);
    }
}
