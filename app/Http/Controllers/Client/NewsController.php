<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index()
    {
        return Inertia::render('Client/News/Index', [
            'posts' => Post::where('is_published', true)->latest()->get()
        ]);
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->where('is_published', true)->firstOrFail();
        
        return Inertia::render('Client/News/Show', [
            'post' => $post,
            'recent_posts' => Post::where('is_published', true)
                ->where('id', '!=', $post->id)
                ->latest()
                ->limit(5)
                ->get()
        ]);
    }
}
