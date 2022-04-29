<?php

namespace App\Http\Controllers\Nova;

use App\Http\Controllers\Controller;

class LocaleController extends Controller
{
    public function handle($locale)
    {
        $available_locales = config('nova.locales');
        abort_unless(isset($available_locales[ $locale ]), 404);

        session(compact('locale'));
        setCurrentLocale($locale);

        if( auth()->check() ) {
            auth()->user()->updateLocale($locale);
        }

        return redirect(back()->getTargetUrl());
    }
}
