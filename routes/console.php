<?php

use App\Jobs\ImportSource;
use App\Models\Post;
use App\Models\Source;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('news:collect', function () {
    $count = 0;
    foreach (Source::where('enabled', true)->get() as $source) {
        if (! $source->last_checked_at || $source->last_checked_at->addMinutes($source->interval_minutes)->lte(now())) {
            ImportSource::dispatch($source->id);
            $count++;
        }
    }
    $this->info("Queued {$count} source(s).");
})->purpose('Queue enabled news sources that are due for an update');
Artisan::command('news:publish', function () {
    $count = Post::where('status', 'scheduled')->where('published_at', '<=', now())->update(['status' => 'published']);
    $this->info("Published {$count} scheduled article(s).");
})->purpose('Publish articles whose scheduled time has arrived');
Artisan::command('news:admin {email?}', function () {
    $email = $this->argument('email') ?: $this->ask('Admin email');
    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('Invalid email.');

        return 1;
    }
    $password = $this->secret('Admin password (hidden)');
    if (! $password || strlen($password) < 5) {
        $this->error('Use at least 5 characters for local testing.');

        return 1;
    }
    $user = User::firstOrNew(['email' => $email]);
    $user->name = $user->name ?: 'Mehedi';
    $user->password = $password;
    $user->is_admin = true;
    $user->save();
    $this->info('Admin account saved.');
})->purpose('Create or update a local administrator without storing a plaintext password');
Schedule::command('news:collect')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('news:publish')->everyMinute()->withoutOverlapping();

Artisan::command('news:storage', function () {
    if (is_link(public_path('storage'))) {
        $this->info('Upload storage link already exists.');
        return 0;
    }
    if (file_exists(public_path('storage'))) {
        $this->error('public/storage is an existing file or directory. Move it safely before creating the upload link.');
        return 1;
    }
    return $this->call('storage:link');
})->purpose('Create the upload link without overwriting existing files');
