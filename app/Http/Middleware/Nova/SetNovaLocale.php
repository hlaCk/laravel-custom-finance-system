<?php

namespace App\Http\Middleware\Nova;

use Closure;
use Illuminate\Http\Request;

class SetNovaLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $default    = config( 'nova.default_locale', 'en' );
        $locales    = config( 'nova.locales', [] );
        if ( auth()->check() )
        {
            $locale = auth()->user()->locale;
            if ( isset( $locales[ $locale ] ) )
            {
                setCurrentLocale( $locale );
            }
            else
            {
                setCurrentLocale( $default );
            }
        }
        else
        {
            $locale = session( 'locale' );
            if ( isset( $locales[ $locale ] ) )
            {
                setCurrentLocale( $locale );
                session([ 'locale' => $locale ]);
            }
            else
            {
                setCurrentLocale( $default );
                session([ 'locale' => $default ]);
            }
        }
        return $next( $request );
    }
}
