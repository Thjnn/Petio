<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = BlogPost::published()
            ->when($request->query('category'), fn ($q, $slug) => $q->whereRelation('category', 'slug', $slug))
            ->when($request->query('tag'), fn ($q, $slug) => $q->whereRelation('tags', 'slug', $slug))
            ->latest('published_at')
            ->paginate(6)
            ->withQueryString();

        return view('blog.index', ['posts' => $posts] + $this->sidebar());
    }

    public function show(BlogPost $post)
    {
        abort_unless($post->status === 'published' && $post->published_at?->isPast(), 404);

        $post->increment('view_count');

        return view('blog.show', ['post' => $post->load('category')] + $this->sidebar());
    }

    private function sidebar(): array
    {
        return [
            'categories' => BlogCategory::orderBy('id')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ];
    }
}
