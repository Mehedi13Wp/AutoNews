<?php

namespace App\Http\Controllers;

use App\Models\ImportRun;
use App\Models\Post;
use App\Models\Source;

class AdminController extends Controller
{
    public function index()
    {
        $activity = collect(range(6, 0))->map(function ($offset) {
            $date = now()->subDays($offset);

            return [
                'label' => $date->translatedFormat('D'),
                'count' => Post::visible()->whereDate('published_at', $date->toDateString())->count(),
            ];
        });
        $max = max(1, $activity->max('count'));
        $activity = $activity->map(fn ($day) => [...$day, 'height' => $day['count'] / $max * 100]);
        $hasOriginalPosts = Post::visible()->where('is_demo', false)->exists();
        $hasSources = Source::exists();
        $aiReady = filled(config('news.ai_key'));

        return view('admin.dashboard', [
            'posts' => Post::with('category')->latest()->limit(6)->get(),
            'runs' => ImportRun::latest()->limit(4)->get(),
            'activity' => $activity,
            'hasOriginalPosts' => $hasOriginalPosts,
            'hasSources' => $hasSources,
            'aiReady' => $aiReady,
            'setupDone' => (int) $hasOriginalPosts + (int) $hasSources + (int) $aiReady,
        ]);
    }
}
