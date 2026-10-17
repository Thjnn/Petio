<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogPostController extends Controller
{
    public const STATUSES = ['draft' => 'Nháp', 'published' => 'Đã đăng', 'hidden' => 'Đã ẩn'];

    public function index(Request $request)
    {
        $posts = BlogPost::with('category')->latest('id')
            ->when($request->q, fn ($q, $term) => $q->where('title', 'like', "%{$term}%"))
            ->paginate(20)->withQueryString();

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return $this->form(new BlogPost(['status' => 'draft']));
    }

    public function store(Request $request)
    {
        $post = new BlogPost(['author_id' => $request->user()->id]);
        $this->save($request, $post);

        return redirect()->route('admin.posts.edit', $post)->with('success', 'Đã thêm bài viết.');
    }

    public function edit(BlogPost $post)
    {
        return $this->form($post->load('tags'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $this->save($request, $post);

        return back()->with('success', 'Đã lưu bài viết.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Đã xóa bài viết.');
    }

    private function form(BlogPost $post)
    {
        return view('admin.posts.form', [
            'post' => $post,
            'categories' => BlogCategory::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    private function save(Request $request, BlogPost $post): void
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:280', Rule::unique('blog_posts')->ignore($post->id)],
            'category_id' => 'nullable|exists:blog_categories,id',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => ['required', Rule::in(array_keys(self::STATUSES))],
            'published_at' => 'nullable|date',
            'thumbnail' => [$post->thumbnail_url ? 'nullable' : 'required', 'image', 'max:4096'],
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ], ['thumbnail.required' => 'Hãy chọn ảnh đại diện cho bài viết.']);

        $post->fill([
            'title' => $data['title'],
            'slug' => ($data['slug'] ?? null) ?: Str::slug($data['title']).($post->exists ? '' : '-'.Str::lower(Str::random(4))),
            'category_id' => $data['category_id'] ?? null,
            'summary' => $data['summary'] ?? null,
            'content' => $data['content'],
            'status' => $data['status'],
            // Đăng bài mà không chọn ngày thì lấy giờ hiện tại
            'published_at' => $data['published_at'] ?? ($data['status'] === 'published' ? ($post->published_at ?? now()) : null),
            'thumbnail_url' => store_upload($request->file('thumbnail'), $post->thumbnail_url),
            'updated_by' => $request->user()->id,
        ])->save();

        $post->tags()->sync($data['tags'] ?? []);
    }
}
