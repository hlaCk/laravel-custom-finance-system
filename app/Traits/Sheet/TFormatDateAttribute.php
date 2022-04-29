<?php

namespace App\Traits\Sheet;

/**
 * @property \Carbon\Carbon $date
 */
trait TFormatDateAttribute
{
    /**
     * Format as ->format() do (using date replacements patterns from https://php.net/manual/en/function.date.php)
     * but translate words whenever possible (months, day names, etc.) using the current locale.
     *
     * @param string|\Closure $format
     *
     * @return string
     */
    public function formatDate($format = 'M/Y'): string
    {
        return $this->date->translatedFormat(value($format, $this->date));
    }
}
