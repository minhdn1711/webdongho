<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Posts/Index', [
            'posts' => Post::latest()->paginate(20)->withQueryString()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Posts/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|string',
        ]);

        Post::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'content' => $request->content,
            'image' => $request->filled('image') ? $request->input('image') : null,
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Tạo bài viết thành công!');
    }

    public function edit(Post $post)
    {
        return Inertia::render('Admin/Posts/Edit', [
            'post' => $post
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'string', 'max:2048'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $post->forceFill([
            'title' => $data['title'],
            'slug' => Str::slug($data['title']) . '-' . $post->id,
            'content' => $data['content'],
            'image' => filled($data['image'] ?? null) ? $data['image'] : null,
            'is_published' => (bool) ($data['is_published'] ?? false),
        ])->save();

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('success', 'Xóa bài viết thành công!');
    }
}
