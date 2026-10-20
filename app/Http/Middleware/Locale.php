<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Locale
{
    public function handle(Request $request, Closure $next)
    {
        app()->setLocale(in_array(session('locale'), ['bn', 'en']) ? session('locale') : 'bn');

        return $next($request);
    }
}
