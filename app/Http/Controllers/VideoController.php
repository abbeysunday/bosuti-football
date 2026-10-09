<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.videos', [
            'videos' => Video::published()->orderByDesc('is_featured')->latest('published_at')->latest('id')->paginate(9),
        ]);
    }
}
