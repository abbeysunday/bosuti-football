<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ManagesUploads;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VideoRequest;
use App\Models\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoController extends Controller
{
    use ManagesUploads;

    public function index(Request $request): View
    {
        return view('admin.videos.index', [
            'videos' => Video::when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%' . $request->input('q') . '%'))
                ->latest('id')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.videos.form', ['video' => new Video()]);
    }

    public function store(VideoRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['thumbnail', 'remove_thumbnail']);
        $data['thumbnail'] = $this->handleUpload($request, 'thumbnail', 'videos');
        $video = Video::create($data);

        return redirect()->route('admin.videos.index')->with('success', "\"{$video->title}\" added.");
    }

    public function edit(Video $video): View
    {
        return view('admin.videos.form', compact('video'));
    }

    public function update(VideoRequest $request, Video $video): RedirectResponse
    {
        $data = $request->safe()->except(['thumbnail', 'remove_thumbnail']);
        $data['thumbnail'] = $this->handleUpload($request, 'thumbnail', 'videos', $video->thumbnail);
        $video->update($data);

        return redirect()->route('admin.videos.index')->with('success', "\"{$video->title}\" updated.");
    }

    public function destroy(Video $video): RedirectResponse
    {
        $this->deleteUpload($video->thumbnail);
        $video->delete();

        return redirect()->route('admin.videos.index')->with('success', "\"{$video->title}\" deleted.");
    }
}
