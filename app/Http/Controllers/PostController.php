<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index(Request $r)
    {
        $q = Post::with('category')->latest();
        if (in_array($r->query('status'), ['draft', 'published', 'scheduled'])) {
            $q->where('status', $r->query('status'));
        }
        if ($r->filled('category')) {
            $q->where('category_id', $r->query('category'));
        }
        if ($r->filled('q')) {
            $q->where(fn ($q) => $q->where('title_en', 'like', '%'.$r->query('q').'%')->orWhere('title_bn', 'like', '%'.$r->query('q').'%'));
        }

        return view('admin.posts', ['posts' => $q->paginate(12)->withQueryString(), 'categories' => Category::all()]);
    }

    public function create()
    {
        return view('admin.post-form', ['post' => new Post, 'categories' => Category::all()]);
    }

    public function edit(Post $post)
    {
        return view('admin.post-form', ['post' => $post, 'categories' => Category::all()]);
    }

    public function store(Request $r)
    {
        $this->save($r, new Post);

        return redirect()->route('admin.posts.index')->with('success', __('Post saved.'));
    }

    public function update(Request $r, Post $post)
    {
        $this->save($r, $post);

        return redirect()->route('admin.posts.index')->with('success', __('Post saved.'));
    }

    private function save(Request $r, Post $post): void
    {
        $data = $r->validate([
            'title_en' => 'required|string|max:250', 'title_bn' => 'required|string|max:250', 'body_en' => 'required|string|max:100000', 'body_bn' => 'required|string|max:100000',
            'excerpt_en' => 'nullable|string|max:1000', 'excerpt_bn' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,id', 'slug' => ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('posts', 'slug')->ignore($post->id)],
            'image_url' => 'nullable|url:http,https|max:2000', 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', 'image_credit' => 'nullable|string|max:250',
            'source_url' => 'nullable|url:http,https|max:2000', 'source_name' => 'nullable|string|max:250',
            'seo_title_en' => 'nullable|string|max:250', 'seo_title_bn' => 'nullable|string|max:250', 'meta_description_en' => 'nullable|string|max:500', 'meta_description_bn' => 'nullable|string|max:500',
            'status' => ['required', Rule::in(['draft', 'published', 'scheduled'])], 'published_at' => ['nullable', 'date', Rule::requiredIf($r->input('status') === 'scheduled')],
        ]);
        unset($data['image']);
        $data['slug'] = ($data['slug'] ?? null) ?: ($post->slug ?: Str::slug($data['title_en']).'-'.Str::lower(Str::random(6)));
        $data['featured'] = $r->boolean('featured');
        $data['is_demo'] = $r->boolean('is_demo');
        if ($data['status'] === 'published') {
            $data['published_at'] = $post->status === 'published' && $post->published_at ? $post->published_at : now();
        }
        if ($data['status'] === 'draft') {
            $data['published_at'] = null;
        }
        if ($data['status'] === 'scheduled' && strtotime($data['published_at']) <= time()) {
            abort(422, __('Choose a future publication time.'));
        }
        if ($r->hasFile('image')) {
            $data['image_path'] = $r->file('image')->store('news', 'public');
        } elseif ($r->boolean('remove_image')) {
            $data['image_path'] = null;
        }
        $post->fill($data)->save();
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return back()->with('success', __('Post deleted.'));
    }
}
