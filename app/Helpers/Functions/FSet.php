<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

/**
 * change app Locale
 */
if( !function_exists('setCurrentLocale') ) {
    /**
     * change app Locale
     *
     * @param string $locale
     *
     * @return void
     */
    function setCurrentLocale(string $locale): void
    {
        if( !isLocaleAllowed($locale) ) return;

        app()->setLocale($locale);

        setlocale(LC_ALL, $locale_cp = config("nova.locales_cp.{$locale}", 'en'));

        if( preg_match('/[a-z]{2}_[A-Z]{2}/s', $locale_cp, $locales_cp) ) {
            config()->set('app.faker_locale', head($locales_cp));
        }

        // Localization Carbon
        \Carbon\Carbon::setLocale(config("nova.force_carbon_locale", $locale));
    }
}
