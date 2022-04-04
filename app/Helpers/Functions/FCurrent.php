<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

/**
 * return appLocale
 */
if( !function_exists('currentLocale') ) {
    /**
     * return appLocale
     *
     * @param bool $full
     *
     * @return string
     */
    function currentLocale($full = false): string
    {
        if( $full )
            return (string) app()->getLocale();

        $locale = str_replace('_', '-', app()->getLocale());
        $locale = current(explode("-", $locale));

        return $locale ?: "";
    }
}

if( !function_exists('currentUrl') ) {
    /**
     * Returns current url.
     *
     * @param string|null $key    return as array with key $key and value as url
     * @param bool        $encode use urlencode
     *
     * @return string|array
     */
    function currentUrl(?string $key = null, bool $encode = true)
    {
        $url = request()->url();
        $url = iif($encode, urlencode($url), $url);

        return is_null($key) ? $url : [ $key => $url ];
    }
}
