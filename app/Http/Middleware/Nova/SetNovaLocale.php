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
        $locales_cp = config( 'nova.locales_cp', [] );
        if ( auth()->check() )
        {
            $locale = auth()->user()->locale;
            if ( isset( $locales[ $locale ] ) )
            {
                app()->setLocale( $locale );
                if ( isset( $locales_cp[ $locale ] ) )
                {
                    setlocale( LC_ALL, $locales_cp[ $locale ] );
                }
            }
            else
            {
                app()->setLocale( $default );
                if ( isset( $locales_cp[ $default ] ) )
                {
                    setlocale( LC_ALL, $locales_cp[ $default ] );
                }
            }
        }
        else
        {
            $locale = session( 'locale' );
            if ( isset( $locales[ $locale ] ) )
            {
                app()->setLocale( $locale );
                session([ 'locale' => $locale ]);
                if ( isset( $locales_cp[ $locale ] ) )
                {
                    setlocale( LC_ALL, $locales_cp[ $locale ] );
                }
            }
            else
            {
                app()->setLocale( $default );
                session([ 'locale' => $default ]);
                if ( isset( $locales_cp[ $default ] ) )
                {
                    setlocale( LC_ALL, $locales_cp[ $default ] );
                }
            }
        }
        return $next( $request );
    }
}
