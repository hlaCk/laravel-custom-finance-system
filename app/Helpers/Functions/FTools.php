<?php
/*
 * Copyright © 2020. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

/** @noinspection ForgottenDebugOutputInspection */

if( !function_exists('carbon') ) {
    /**
     * @return \Carbon\Carbon|\Illuminate\Foundation\Application|mixed
     */
    function carbon()
    {
        return app(\Carbon\Carbon::class);
    }
}

if( !function_exists('when') ) {
    /**
     * if $condition then call $whenTrue|null else call $whenFalse|null
     *
     * @param bool|mixed    $condition
     * @param callable|null $whenTrue
     * @param callable|null $whenFalse
     * @param mixed|null    $with
     *
     * @return mixed|null
     */
    function when($condition, callable $whenTrue = null, callable $whenFalse = null, $with = null)
    {
        if( value($condition) ) {
            return is_callable($whenTrue) ? $whenTrue($condition, $with) : $whenTrue;
        } else {
            return is_callable($whenFalse) ? $whenFalse($condition, $with) : $whenFalse;
        }
    }
}

// region: return
if( !function_exists('returnCallable') ) {
    /**
     * Determine if the given value is callable, but not a string.
     * **Source**: ---  {@link \Illuminate\Support\Collection Laravel Collection}
     *
     * @param mixed $value
     *
     * @return \Closure
     */
    function returnCallable($value): \Closure
    {
        if( !is_callable($value) ) {
            return returnClosure($value);
        }

        if( is_string($value) ) {
            return Closure::fromCallable($value);
        }

        return $value;
    }
}

if( !function_exists('returnClosure') ) {
    /**
     * Returns function that returns any arguments u sent;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnClosure(...$data)
    {
        $_data = head($data);
        if( func_num_args() > 1 ) {
            $_data = $data;
        } elseif( func_num_args() === 0 ) {
            $_data = returnNull();
        }

        return function() use ($_data) {
            return value($_data);
        };
    }
}

if( !function_exists('returnArray') ) {
    /**
     * Returns function that returns [];
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnArray(...$data)
    {
        return returnClosure($data);
    }
}

if( !function_exists('returnCollect') ) {
    /**
     * Returns function that returns Collection;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnCollect(...$data)
    {
        return function(...$args) use ($data) {
            return collect($data)->merge($args);
        };
    }
}

if( !function_exists('returnArgs') ) {
    /**
     * Returns function that returns func_get_args();
     *
     * @return \Closure
     */
    function returnArgs()
    {
        return function() {
            return func_get_args();
        };
    }
}

if( !function_exists('returnString') ) {
    /**
     * Returns function that returns ""
     *
     * @param string|null $text
     *
     * @return \Closure
     */
    function returnString(?string $text = "")
    {
        return returnClosure((string) $text);
    }
}

if( !function_exists('returnNull') ) {
    /**
     * Returns function that returns null;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnNull()
    {
        return function() {
            return null;
        };
    }
}

if( !function_exists('returnTrue') ) {
    /**
     * Returns function that returns true;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnTrue()
    {
        return returnClosure(true);
    }
}

if( !function_exists('returnFalse') ) {
    /**
     * Returns function that returns false;
     *
     * @param mixed ...$data
     *
     * @return \Closure
     */
    function returnFalse()
    {
        return returnClosure(false);
    }
}
// endregion: return

#region IS
if( !function_exists('firstSet') ) {
    /**
     * @param mixed ...$var
     *
     * @return mixed|null
     */
    function firstSet(...$var)
    {
        foreach( $var as $_var )
            if( isset($_var) )
                return $_var;

        return null;
    }
}

if( !function_exists('getAny') ) {
    /**
     * @param mixed ...$vars
     *
     * @return mixed|null
     */
    function getAny(...$vars)
    {
        foreach( $vars as $_var ) {
            if( $_var ) {
                return $_var;
            }
        }

        return null;
    }
}

if( !function_exists('test') ) {
    /**
     * Apply `value` function to each argument. when value returns something true ? return it.
     *
     * @param mixed ...$vars
     *
     * @return mixed|null
     */
    function test(...$vars)
    {
        foreach( $vars as $_var )
            if( $_var = value($_var) ) {
                return $_var;
            }

        return null;
    }
}

if( !function_exists('iif') ) {
    /**
     * Test Condition and return one of two parameters
     *
     * @param mixed $var   Condition
     * @param mixed $true  Return this if Condition == true
     * @param mixed $false Return this when Condition fail
     *
     * @return mixed
     */
    function iif($var, $true = null, $false = null)
    {
        return value(value($var) ? $true : $false);
    }
}
#endregion

#region HAS
if( !function_exists('hasTrait') ) {
    /**
     * Check if given class has trait.
     *
     * @param mixed  $class     <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $traitName <p>
     *                          Trait name to check
     *                          </p>
     *
     * @return bool
     */
    function hasTrait($class, $traitName)
    {
        try {
            $traitName = str_contains($traitName, "\\") ? class_basename($traitName) : $traitName;

            $hasTraitRC = new ReflectionClass($class);
            $hasTrait = collect($hasTraitRC->getTraitNames())->map(function($name) use ($traitName) {
                    $name = str_contains($name, "\\") ? class_basename($name) : $name;

                    return $name == $traitName;
                })->filter()->count() > 0;
        } catch(ReflectionException $exception) {
            $hasTrait = false;
        } catch(Exception $exception) {
            // dd($exception->getMessage());
            $hasTrait = false;
        }

        return $hasTrait;
    }
}

if( !function_exists('hasKey') ) {
    /**
     * Check if given array has key if has key call $callable.
     *
     * @param array        $array
     * @param string       $key
     * @param Closure|null $callable
     *
     * @return bool|mixed
     */
    function hasKey($array, $key, Closure $callable = null)
    {
        try {
            $has = array_key_exists($key, $array);
            if( $callable && is_callable($callable) ) {
                return $callable->call($array, $array);
            }

            return $has === true;
        } catch(Exception $exception) {
            // d($exception->getMessage());
        }

        return false;
    }
}

if( !function_exists('hasScope') ) {
    /**
     * Check if given class has the given scope name.
     *
     * @param mixed  $class     <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $scopeName <p>
     *                          Scope name to check
     *                          </p>
     *
     * @return bool
     */
    function hasScope($class, $scopeName)
    {
        try {
            $hasScopeRC = new ReflectionClass($class);
            $scopeName = strtolower(studly_case($scopeName));
            starts_with($scopeName, "scope") && ($scopeName = substr($scopeName, strlen("scope")));

            $hasScope = collect($hasScopeRC->getMethods())->map(function($c) use ($scopeName) {
                    /**
                     * @var $c ReflectionMethod
                     */
                    $name = strtolower(studly_case($c->getName()));
                    $name = starts_with($name, "scope") ? substr($name, strlen("scope")) : false;

                    return $name == $scopeName;
                })->filter()->count() > 0;
        } catch(ReflectionException $exception) {
            $hasScope = false;
        } catch(Exception $exception) {
            $hasScope = false;
        }

        return ! !$hasScope;
    }
}

if( !function_exists('hasConst') ) {
    /**
     * Check if given class has the given const.
     *
     * @param mixed  $class     <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $const     <p>
     *                          Const name to check
     *                          </p>
     *
     * @return bool
     */
    function hasConst($class, $const): bool
    {
        $hasConst = false;
        try {
            if( is_object($class) || is_string($class) ) {
                $reflect = new ReflectionClass($class);
                $hasConst = array_key_exists($const, $reflect->getConstants());
            }
        } catch(ReflectionException $exception) {
            $hasConst = false;
        } catch(Exception $exception) {
            $hasConst = false;
        }

        return (bool) $hasConst;
    }
}

if( !function_exists('hasStaticProp') ) {
    /**
     * Check if given class has the given Static Property.
     *
     * @param mixed  $class     <p>
     *                          Either a string containing the name of the class to
     *                          check, or an object.
     *                          </p>
     * @param string $const     <p>
     *                          Static Property name to check
     *                          </p>
     *
     * @return bool
     */
    function hasStaticProp($class, $const): bool
    {
        $hasStaticProp = false;
        try {
            if( is_object($class) || is_string($class) ) {
                $reflect = new ReflectionClass($class);
                $hasStaticProp = array_key_exists($const, $reflect->getStaticProperties());
            }
        } catch(ReflectionException $exception) {
            $hasStaticProp = false;
        } catch(Exception $exception) {
            $hasStaticProp = false;
        }

        return (bool) $hasStaticProp;
    }
}

if( !function_exists('getConst') ) {
    /**
     * Returns const value if exists, otherwise returns $default.
     *
     * @param string|array $const   <p>
     *                              Const name to check
     *                              </p>
     * @param mixed|null   $default <p>
     *                              Value to return when const not found
     *                              </p>
     *
     * @return mixed
     */
    function getConst($const, $default = null)
    {
        return defined($const = is_array($const) ? implode("::", $const) : $const) ? constant($const) : $default;
    }
}
#endregion

#region GET
if( !function_exists('str_prefix') ) {
    /**
     * Add a prefix to string but only if string2 is not empty.
     *
     * @param string      $string  string to prefix
     * @param string      $prefix  prefix
     * @param string|null $string2 string2 to prefix the return
     *
     * @return string|null
     */
    function str_prefix($string, $prefix, $string2 = null)
    {
        $newString = rtrim(is_null($string2) ? '' : $string2, $prefix) .
            $prefix .
            ltrim($string, $prefix);

        return ltrim($newString, $prefix);
    }
}

if( !function_exists('str_suffix') ) {
    /**
     * Add a suffix to string but only if string2 is not empty.
     *
     * @param string      $string  string to suffix
     * @param string      $suffix  suffix
     * @param string|null $string2 string2 to suffix the return
     *
     * @return string|null
     */
    function str_suffix($string, $suffix, $string2 = null)
    {
        $newString = ltrim($string, $suffix) . $suffix . rtrim(is_null($string2) ? '' : $string2, $suffix);

        return trim($newString, $suffix);
    }
}

if( !function_exists('str_words_limit') ) {
    /**
     * Limit string words.
     *
     * @param string      $string string to limit
     * @param int         $limit  word limit
     * @param string|null $suffix suffix the string
     *
     * @return string
     */
    function str_words_limit($string, $limit, $suffix = '...')
    {
        $start = 0;
        $stripped_string = strip_tags($string); // if there are HTML or PHP tags
        $string_array = explode(' ', $stripped_string);
        $truncated_array = array_splice($string_array, $start, $limit);

        $lastWord = end($truncated_array);
        $return = substr($string, 0, stripos($string, $lastWord) + strlen($lastWord)) . ' ' . $suffix;

        $m = [];
        if( preg_match_all('#<(\w+).+?#is', $return, $m) ) {
            $m = is_array($m) && is_array($m[ 1 ]) ? array_reverse($m[ 1 ]) : [];
            foreach( $m as $HTMLTAG ) {
                $return .= "</{$HTMLTAG}>";
            }
        }

        return $return;
    }
}

if( !function_exists('getTrans') ) {
    /**
     * Translate the given message or return default.
     *
     * @param string|null $key
     * @param array       $replace
     * @param string|null $locale
     *
     * @return string|array|null
     */
    function getTrans($key = null, $default = null, $replace = [], $locale = null)
    {
        $key = value($key);
        $return = __($key, $replace, $locale);

        return $return === $key ? value($default) : $return;
    }
}
#endregion

if( !function_exists('getRecommendedSizeTrans') ) {
    /**
     * @param        $n1     Number
     * @param        $n2     Number
     * @param string $prepend
     * @param null   $locale 'ar' or 'en'
     * @param string $size_separator
     * @param string $prefix
     * @param string $suffix
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|mixed|string|null
     */
    function getRecommendedSizeTrans($n1, $n2, $prepend = '', $locale = null, $size_separator = 'x', $prefix = '(', $suffix = ')')
    {
        return getCustomRecommendedSizeTrans('common.recommended_size', $n1, $n2, $prepend, $locale, $size_separator, $prefix, $suffix);
    }
}

if( !function_exists('getCustomRecommendedSizeTrans') ) {
    /**
     * @param string|null $title
     * @param             $n1     Number
     * @param             $n2     Number
     * @param string      $prepend
     * @param null        $locale 'ar' or 'en'
     * @param string      $size_separator
     * @param string      $prefix
     * @param string      $suffix
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|mixed|string|null
     */
    function getCustomRecommendedSizeTrans(?string $title = null, $n1, $n2, $prepend = '', $locale = null, $size_separator = 'x', $prefix = '(', $suffix = ')')
    {
        $prepend = $prepend ? "{$prepend} " : "";

        return $prepend .
            $prefix .
            __($title ?? 'common.recommended_size', [
                'n1' => $n1,
                'separator' => $size_separator,
                'n2' => $n2,
            ], $locale ?: currentLocale()) .
            $suffix;
    }
}

if( !function_exists('localeWrap') ) {
    /**
     * Return the given value wrapped in array with locales as key.
     *
     * @param mixed      $value
     * @param array|null $locales
     *
     * @return array
     */
    function localeWrap($value = null, ?array $locales = null): array
    {
        $locales ??= array_keys(config('nova.locales'));
        $_attributes = is_array($value) ? $value : [];
        foreach( $locales as $locale ) {
            $_attributes[ $locale ] = $_attributes[ $locale ] ?? $value;
        }

        return $_attributes;
    }
}
