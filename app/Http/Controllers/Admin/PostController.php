<?php

namespace App\Http\Controllers\Admin;

use App\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
    }

    public function index()
    {
        $posts = Post::query()
            ->with('author')
            ->unless(auth()->user()->can('view-all', Post::class), function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->paginate();

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return 'New post';
    }

    public function store(Request $request)
    {
        $request->user()->posts()->create([
            'title' => $request->title,
        ]);

        return new Response('Post created', 201);
    }

    public function edit(Post $post)
    {
        return 'Edit post';
    }

    public function update(Post $post, UpdatePostRequest $request)
    {
        $post->update([
            'title' => $request->title,
        ]);

        return 'Post updated!';
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect('admin/posts');
    }
}
