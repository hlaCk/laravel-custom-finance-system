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

        setlocale(LC_ALL, config("nova.locales_cp.{$locale}", 'en'));

        // Localization Carbon
        \Carbon\Carbon::setLocale(config("nova.force_carbon_locale", $locale));
    }
}
