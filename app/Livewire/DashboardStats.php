<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Source;
use Livewire\Component;

class DashboardStats extends Component
{
    public function render()
    {
        abort_unless(auth()->user()?->is_admin, 403);

        return view('livewire.dashboard-stats', [
            'total' => Post::count(), 'published' => Post::visible()->count(),
            'drafts' => Post::where('status', 'draft')->count(), 'activeSources' => Source::where('enabled', true)->count(),
        ]);
    }
}
