<?php

namespace App\Http\Middleware;

use Closure;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headerLang = request()->header('Accept-Language') ?? 'en';
        $headerLang = config("nova.locales.{$headerLang}") ? $headerLang : 'en';
        app()->setLocale($headerLang);

        return $next($request);
    }
}
