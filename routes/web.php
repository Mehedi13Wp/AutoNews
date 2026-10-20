<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SourceController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('/', [NewsController::class, 'index'])->name('home');
Route::get('/category/{category:slug}', [NewsController::class, 'index'])->name('category');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::post('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['bn', 'en']), 404);
    session(['locale' => $locale]);

    return back();
})->name('language');
Route::middleware('guest')->group(function () {
    Route::view('/admin/login', 'admin.login')->name('login');
    Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
});
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('posts', PostController::class)->except('show');
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);
    Route::resource('sources', SourceController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/sources/{source}/run', [SourceController::class, 'run'])->middleware('throttle:10,1')->name('sources.run');
});
Route::get('/sitemap.xml', function () {
    $posts = Post::visible()->latest('published_at')->limit(10000)->get();

    return response()->view('news.sitemap', compact('posts'))->header('Content-Type','application/xml');
})->name('sitemap');
