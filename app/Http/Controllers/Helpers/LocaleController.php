<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function handle(Request $request, $locale)
    {
        $avail_locales = config('nova.locales');

        if( isset($avail_locales[ $locale ]) ) {
            session([ 'locale' => $locale ]);
            session()->save();
            /** @var \App\Models\User $user */
            // if( ($user = currentUser()) && $user->locale != $locale ) {
            //     $user->updateLocale($locale);
            // }
            app()->setLocale($locale);
        } else {
            return abort(404);
        }

        return redirect(back()->getTargetUrl());
    }
}
