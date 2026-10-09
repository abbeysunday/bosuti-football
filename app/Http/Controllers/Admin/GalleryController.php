<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesUploads;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryItemRequest;
use App\Models\Fixture;
use App\Models\GalleryItem;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    use ManagesUploads;

    public function index(Request $request): View
    {
        return view('admin.gallery.index', [
            'items' => GalleryItem::with(['fixture.homeTeam', 'fixture.awayTeam', 'team'])
                ->when(array_key_exists((string) $request->input('category'), GalleryItem::CATEGORIES), fn ($q) => $q->where('category', $request->input('category')))
                ->ordered()
                ->paginate(24)
                ->withQueryString(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.gallery.form', [
            'item' => new GalleryItem(['fixture_id' => $request->integer('fixture') ?: null]),
            ...$this->options(),
        ]);
    }

    /** Uploads one or more photos sharing the same details. */
    public function store(GalleryItemRequest $request): RedirectResponse
    {
        $shared = $request->safe()->except(['images', 'image']);

        foreach ($request->file('images') as $file) {
            GalleryItem::create($shared + ['image' => $this->storeUpload($file, 'gallery')]);
        }

        $count = count($request->file('images'));

        return redirect()->route('admin.gallery.index')->with('success', $count === 1 ? 'Photo uploaded.' : "{$count} photos uploaded.");
    }

    public function edit(GalleryItem $galleryItem): View
    {
        return view('admin.gallery.form', ['item' => $galleryItem, ...$this->options()]);
    }

    public function update(GalleryItemRequest $request, GalleryItem $galleryItem): RedirectResponse
    {
        $data = $request->safe()->except(['images', 'image']);

        if ($request->hasFile('image')) {
            $this->deleteUpload($galleryItem->image);
            $data['image'] = $this->storeUpload($request->file('image'), 'gallery');
        }

        $galleryItem->update($data);

        return redirect()->route('admin.gallery.index')->with('success', 'Photo updated.');
    }

    public function destroy(GalleryItem $galleryItem): RedirectResponse
    {
        $this->deleteUpload($galleryItem->image);
        $galleryItem->delete();

        return back()->with('success', 'Photo deleted.');
    }

    private function options(): array
    {
        return [
            'fixtures' => Fixture::withTeams()->latestFirst()->limit(100)->get()
                ->mapWithKeys(fn (Fixture $f) => [$f->id => $f->match_date->format('d M Y') . ' — ' . $f->title]),
            'teams' => Team::orderBy('name')->pluck('name', 'id'),
        ];
    }
}
