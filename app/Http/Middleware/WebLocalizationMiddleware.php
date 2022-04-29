<?php

namespace App\Http\Middleware;

use Closure;

class WebLocalizationMiddleware
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
        $lang = request('lang', session('locale', config('app.locale', 'en')));
        $lang = config("nova.locales.{$lang}") ? $lang : 'en';
        setCurrentLocale($lang);

        $session = session();
        $session->put([ 'locale' => $lang ]);
        $session->save();

        if( ($user = $request->user()) && $user->locale != $lang) {
            /** @var \App\Models\Info\User $user */
            $user->updateLocale($lang);
        }
        $query = ($query = http_build_query(array_except($_GET, 'lang'))) ? "?{$query}" : "";

        return request()->has('lang') && !request()->expectsJson() ? redirect()->to(currentUrl(null, false) . $query)
            : $next($request);
    }
}
