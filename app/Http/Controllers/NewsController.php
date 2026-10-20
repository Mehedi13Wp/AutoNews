<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $r, ?Category $category = null)
    {
        $q = Post::visible()->with('category')->orderByDesc('featured')->latest('published_at');
        if ($category) {
            $q->where('category_id', $category->id);
        }
        $search = trim((string) $r->query('q', ''));
        if ($search !== '') {
            $q->where(function ($q) use ($search) {
                $q->where('title_en', 'like', '%'.$search.'%')->orWhere('title_bn', 'like', '%'.$search.'%')->orWhere('body_en', 'like', '%'.$search.'%')->orWhere('body_bn', 'like', '%'.$search.'%');
            });
        }

        return view('news.index', ['posts' => $q->paginate(13)->withQueryString(), 'category' => $category, 'search' => $search]);
    }

    public function show(string $slug)
    {
        $post = Post::visible()->with('category')->where('slug', $slug)->firstOrFail();

        return view('news.show', ['post' => $post, 'related' => Post::visible()->with('category')->where('category_id', $post->category_id)->whereKeyNot($post->id)->latest('published_at')->limit(3)->get()]);
    }
}
