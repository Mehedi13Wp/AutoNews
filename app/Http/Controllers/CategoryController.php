<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Source;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories', ['categories' => Category::withCount('posts')->get()]);
    }

    public function store(Request $r)
    {
        Category::create($r->validate(['name_en' => 'required|string|max:80', 'name_bn' => 'required|string|max:80', 'slug' => 'required|alpha_dash:ascii|max:80|unique:categories']));

        return back()->with('success', __('Category added.'));
    }

    public function destroy(Category $category)
    {
        if ($category->posts()->exists() || Source::where('category_id', $category->id)->exists()) {
            return back()->withErrors(['category' => __('This category is used by posts or sources.')]);
        }
        $category->delete();

        return back()->with('success', __('Category deleted.'));
    }
}
