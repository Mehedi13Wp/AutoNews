<?php

namespace App\Http\Controllers;

use App\Jobs\ImportSource;
use App\Models\Category;
use App\Models\ImportRun;
use App\Models\Source;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    public function index()
    {
        return view('admin.sources', ['sources' => Source::with('category')->get(), 'categories' => Category::all(), 'runs' => ImportRun::latest()->limit(15)->get(), 'aiReady' => filled(config('news.ai_key'))]);
    }

    public function store(Request $r)
    {
        Source::create($this->data($r));

        return back()->with('success', __('Source saved.'));
    }

    public function update(Request $r, Source $source)
    {
        $source->update($this->data($r));

        return back()->with('success', __('Source saved.'));
    }

    private function data(Request $r): array
    {
        $data = $r->validate(['name' => 'required|string|max:120', 'feed_url' => 'required|url:https|max:2000', 'category_id' => 'required|exists:categories,id', 'keywords' => 'nullable|string|max:1000', 'interval_minutes' => 'required|integer|min:5|max:1440', 'max_posts' => 'required|integer|min:1|max:10']);
        foreach (['enabled', 'auto_publish', 'image_rights_confirmed'] as $f) {
            $data[$f] = $r->boolean($f);
        }

        return $data;
    }

    public function run(Source $source)
    {
        if (! config('news.ai_key')) {
            return back()->withErrors(['ai' => __('Add NEWS_AI_API_KEY to your local .env before importing.')]);
        }
        ImportSource::dispatch($source->id);

        return back()->with('success', __('Import queued. Start the queue worker to process it.'));
    }

    public function destroy(Source $source)
    {
        $source->delete();

        return back()->with('success', __('Source deleted.'));
    }
}
