<?php

use App\Models\Info\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\TrashedStatus;

/*
function isSupport(): bool
{
    return auth()->user() && in_array(auth()->user()->user_type, [ 'support', 'developer' ]);
}

function isAdmin(): bool
{
    return auth()->user() && auth()->user()->user_type == 'admin';
}*/

function convert_to_en_numbers($string)
{
    $persian = [ '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ];
    // $arabic = [ '٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١', '٠' ];
    $arabic = [ '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ];

    $num = range(0, 9);
    $convertedPersianNums = str_replace($persian, $num, $string);
    $englishNumbersOnly = str_replace($arabic, $num, $convertedPersianNums);

    return $englishNumbersOnly;
}

function format_phone($phone)
{
    // remove empty spaces
    $phone = str_replace(' ', '', $phone);
    // remove country code & replace it with 0
    $phone = preg_replace('/^\+966/', '0', $phone);
    $phone = preg_replace('/^00966/', '0', $phone);
    $phone = preg_replace('/^966/', '0', $phone);

    // convert_to_en_numbers
    $phone = convert_to_en_numbers($phone);

    return $phone;
}

function format_and_validate_phone($phone)
{
    $phone = format_phone($phone);

    // validate number
    $res = preg_match('/^05[0-9]{8}+$/', $phone);

    if( !$res ) {
        throw new Exception(__('api.valid phone number format'), 1);
    }

    return $phone;
}

function imageResize($imageData, ?string $newPath = 'images')
{
    $image_normal = Image::make($imageData);
    $image_thumbnail = Image::make($imageData);
    $extension = explode('/', $image_normal->mime())[ 1 ];
    $fileName = $newPath . '/' . now()->timestamp . '_' . uniqid();

    if( $newPath ) {
        $pathinfo = pathinfo($newPath);
        if( isset($pathinfo[ 'extension' ]) ) {
            $extension = $pathinfo[ 'extension' ];
        }
        if( $pathinfo[ 'dirname' ] === '.' ) {
            $pathinfo[ 'dirname' ] = $newPath;
            $pathinfo[ 'filename' ] = basename($fileName);
        }
        $fileName = "{$pathinfo['dirname']}/{$pathinfo['filename']}";
    }

    if( $image_normal->width() > 750 ) {
        $image_normal = $image_normal->resize(750, null, function ($constraint) {
            $constraint->aspectRatio();
        });
    }
    if( $image_thumbnail->width() > 320 ) {
        $image_thumbnail = $image_thumbnail->resize(320, null, function ($constraint) {
            $constraint->aspectRatio();
        });
    }

    $image_normal = $image_normal->encode($extension, 85)
                                 ->stream();
    $image_thumbnail = $image_thumbnail->encode($extension, 75)
                                       ->stream();

    Storage::put("public/{$fileName}.{$extension}", $image_normal->__toString());
    Storage::put("public/{$fileName}_thumbnail.{$extension}", $image_thumbnail->__toString());

    return $fileName . '.' . $extension;
}

function imageThumbnail($image = null)
{
    if( !$image ) return null;
    $exs = pathinfo($image, PATHINFO_EXTENSION);
    $thumbnail = "_thumbnail.{$exs}";

    return str_replace(".{$exs}", $thumbnail, $image);
}

function file_url($path)
{
    if( !$path ) return asset('app/images/not-available.png');
    if( \Illuminate\Support\Str::startsWith(strtolower($path), 'http') ) {
        return $path;
    }

    return asset($path);
    /* $prefix = starts_with($path, '/storage/') || starts_with($path, 'storage/') ? '' : 'storage/';
     $pathWithPrefix = $prefix . $path;
     $pathWithPrefix = starts_with($pathWithPrefix, '/') ? $pathWithPrefix : "/{$pathWithPrefix}";
     $bucket = "https://s3.amazonaws.com/riyaadi";
     return $bucket . $pathWithPrefix;*/
}

function s_public_path($path = null)
{
    return storage_path('app/public/' . $path);
}

function getTranslatedField($model, $field, $value)
{
    if( app()->getLocale() == 'en' && $model->{$field . '_en'} !== null ) {
        return $model->{$field . '_en'};
    } else {
        return $value;
    }
}

function getDefaultDiskDriver($default = 'local'): string
{
    return config('filesystems.default', ($default ?: 'public'));
}

/**
 * Check if the current request is 'create new model' or 'index'
 * **use it in nova requests**
 *
 * @return bool
 */
function isNovaRequestCreateOrListMode(): bool
{
    return request()->resourceId === null;
}

/**
 * @param string        $class
 * @param callable|null $callback
 *
 * @return string
 */
function getClassUriKey(string $class, callable $callback = null): string
{
    $class = Str::plural(Str::kebab(class_basename($class)));

    return is_callable($callback) ? value($callback, $class) : $class;
}

/**
 * @param array|\Illuminate\Support\Collection $aArray1
 * @param array|\Illuminate\Support\Collection $aArray2
 *
 * @return array The differences
 */
function array_recursive_diff($aArray1, $aArray2, callable $callback = null): array
{
    $aArray1 = $aArray1 instanceof \Illuminate\Support\Collection ? $aArray1->toArray() : $aArray1;
    $aArray2 = $aArray2 instanceof \Illuminate\Support\Collection ? $aArray2->toArray() : $aArray2;
    $callback = $callback ?: fn(...$a) => $a;
    $aReturn = [];

    foreach( $aArray1 as $mKey => $mValue ) {
        if( array_key_exists($mKey, $aArray2) ) {
            if( is_array($mValue) ) {
                $aRecursiveDiff = array_recursive_diff($mValue, $aArray2[ $mKey ]);
                if( count($aRecursiveDiff) ) {
                    $aReturn[ $mKey ] = $aRecursiveDiff;
                }
            } else {
                if( $mValue != $aArray2[ $mKey ] ) {
                    $aReturn[ $mKey ] = $callback([
                                                      'expect' => $mValue,
                                                      'actual' => $aArray2[ $mKey ],
                                                  ]);
                }
            }
        } else {
            $aReturn[ $mKey ] = $mValue;
        }
    }

    return $aReturn;
}

/**
 * Expands a dot notation array into a full multi-dimensional array.
 *
 * @param array $array
 *
 * @return array
 */
function undot(array $array)
{
    $result = [];
    foreach( $array as $key => $value ) {
        array_set($result, $key, $value);
    }

    return $result;
}

/**
 * Get the portion of a string before the last occurrence of a given value for X times.
 *
 * @param string $subject
 * @param string $search
 * @param int    $times
 *
 * @return string
 */
function str_before_last_count($subject, $search, int $times = 1): string
{
    $times = $times > 0 ? $times : 0;
    $result = $subject;
    while( $times && $times-- ) {
        $result = \Illuminate\Support\Str::beforeLast($result, $search);
    }

    return $result;
}

/**
 * @param Illuminate\Support\Collection|Laravel\Nova\ResourceCollection|array|null $navigation
 *
 * @return Illuminate\Support\Collection|Laravel\Nova\ResourceCollection|array|null
 */
function navigationRenderCallback($navigation = null)
{
    $_navigation = toCollect($navigation);
    $_navigation_sort = config('navigation.sort', []);
    $_navigation_sorted = [];
    foreach( $_navigation_sort as $group ) {
        $_navigation_sorted[] = $group;
        $trans_group = __($group);
        if( $trans_group != $group ) {
            $_navigation_sorted[] = $trans_group;
        }
    }
    $_sort_by_callback =
        fn($value, $key) => ($new_key = array_search($key, $_navigation_sorted)) === false ? count(
                                                                                                 $_navigation_sorted
                                                                                             ) + (double) $key
            : $new_key;

    return $_navigation->sortBy($_sort_by_callback);
}

/**
 * Check if navigation item should be hidden.
 *
 * @param string $name
 *
 * @return bool
 */
function navigationItemIsHidden(string $name): bool
{
    return in_array($name, config('navigation.hidden', []));
}

if( !function_exists('toCollect') ) {
    /**
     * Create a new collection from the given value if its wasn't collection.
     *
     * @param mixed $value
     *
     * @return \Illuminate\Support\Collection
     */
    function toCollect($value = null): \Illuminate\Support\Collection
    {
        $value = isModel($value) ? [ $value ] : $value;

        return is_collection($value) ? $value : collect($value);
    }
}

/**
 * @return string|null
 */
function getLastCalledClass(): ?string
{
    return data_get(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4), '2.class');
}

/**
 * Return array of int.
 *
 * @param array|\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Model|int $value
 *
 * @return array
 */
function parseArrayOf($value, Closure $mapEach = null, Closure $callback = null): array
{
    $mapEach ??= fn($_value) => modelIdExtractor($_value);
    $callback ??= Closure::fromCallable('value');
    $ids = toCollect($value)
        ->map($mapEach)
        ->all();

    return $callback($ids);
}

/**
 * @param int|\Illuminate\Database\Eloquent\Model|array|object|\Illuminate\Support\Collection $_model
 * @param string                                                                              $key
 * @param callable|mixed|null                                                                 $default
 *
 * @return int
 */
function modelIdExtractor($_model, string $key = 'id', $default = null): int
{
    return (int) isModel($_model) || is_object($_model) || is_array($_model) ?
        data_get($_model, 'id', $default)
        : $_model;
}

function buildFullUrl($path): string
{
    return url(Storage::url($path));
}

/**
 * @param string            $key
 * @param array             $boolKey $key => boolvalue
 * @param string|array|null $passRules
 * @param string|array|null $failRules
 *
 * @return \Closure
 */
function requiredWithBoolean(string $key, array $boolKey, $passRules = null, $failRules = null): Closure
{
    $passRules = (array) $passRules;
    $failRules = (array) $failRules;

    return fn($values) => (
    !data_get($values, $key) && ((bool) data_get($values, key($boolKey))) == head($boolKey)
        ?
        [ "required_with:" . key($boolKey), ...$passRules ]
        :
        [ 'nullable', ...$failRules ]
    );
}

/*
function getOrganizationIdFromHeader(): ?int
{
    return request()->header('organization-id', null);
}

function getOrganizationIdFromUser(\App\Models\Info\User $user = null): ?int
{
    $user ??= auth()->user();
    if( $user && $user->user_type == 'employee' ) {
        return $user->organization_id;
    }

    return null;
}*/

/**
 * Returns the rounded value of val to specified precision (number of digits after the decimal point).
 * precision can also be negative or zero (default).
 * Note: PHP doesn't handle strings like "12,300.2" correctly by default. See converting from strings.
 *
 * @link https://php.net/manual/en/function.round.php
 *
 * @param int|float $number    <p>
 *                             The value to round
 *                             </p>
 * @param int       $precision [optional] <p>
 *                             The optional number of decimal digits to round to.
 *                             </p>
 * @param int       $mode      [optional] <p>
 *                             One of PHP_ROUND_HALF_UP,
 *                             PHP_ROUND_HALF_DOWN,
 *                             PHP_ROUND_HALF_EVEN, or
 *                             PHP_ROUND_HALF_ODD.
 *                             </p>
 *
 * @return float The rounded value
 */
function R($number, int $precision = 2, int $mode = PHP_ROUND_HALF_UP)
{
    return round($number, $precision, $mode);
}

/**
 * @param int|float|string|\Closure $value
 * @param array                     $options [currency = null, locale = null, digits = 2]
 *
 * @return string
 */
function currencyFormat(
    $value,
    array $options = [
        'currency' => null,
        'locale'   => null,
        'digits'   => 2,
    ]
): string {
    $locale = data_get($options, 'locale') ?: config('app.locale', 'en');
    $currency = data_get($options, 'currency') ?: config('nova.currency', 'USD');
    $digits = data_get($options, 'digits') ?: 2;

    $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
    $formatter->setAttribute($formatter::FRACTION_DIGITS, $digits);

    return trim($formatter->formatCurrency(value($value), $currency));
}

/**
 * alias for currencyFormat, default locale is en
 *
 * @param int|float|string|\Closure $value
 * @param array                     $options [currency = null, locale = null, digits = 2]
 *
 * @return string
 */
function currencyFormatEn(
    $value,
    array $options = [
        'currency' => null,
        'locale'   => 'en',
        'digits'   => 2,
    ]
): string {
    return currencyFormat($value, $options);
}

function getSVGPath(string $icon_name): string
{
    return str_before(
        str_after(File::get(resource_path("icons/svg/{$icon_name}.svg")), ' d="'),
        '"'
    );
}

function getSVGIcon(?string $icon_name = 'icon-x', ?string $color = 'currentColor'): string
{
    return view('icon', [
        'icon'  => $icon_name ?: '',
        'color' => $color ?: 'currentColor',
    ])->render();
}

/**
 * @param \Laravel\Nova\Resource|\Illuminate\Database\Eloquent\Model                     $resource
 * @param null|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model $query
 *
 * @return array
 */
function getOrderDropDownOptions($resource, $query = null): array
{
    if( $resource instanceof \Laravel\Nova\Resource ) {
        $query ??= $resource::$model::query();
        $withTrashed =
            method_exists($resource::$model, 'trashed') ? ($resource->model()->trashed() ? TrashedStatus::WITH
                : TrashedStatus::DEFAULT) : '';
        $query = $resource::buildIndexQuery(
            $request = app(NovaRequest::class),
            $query,
            $request->search,
            method_exists($request, 'filters') ? $request->filters()->all() : [],
            method_exists($request, 'orderings') ? $request->orderings() : [],
            $withTrashed
        );
    } else {
        $query ??= $resource::query();
    }
// logger($query->get('sdfsdf'));
    $options = [];
    $count = $query->count();
    // $assigned_orders = $query->get('order')->map->order
    //     ->filter(fn($x) => $x && is_numeric($x))
    //     ->unique()
    //     ->toArray();
    $orders = range(1, $count + 1);
    // $diff = array_diff($orders, $assigned_orders);

    foreach( $orders as $order ) {
        $options[ $order ] = $order;
    }

    if( $resource && $resource->order ) {
        $options[ $resource->order ] = $resource->order;
    }

    return $options;
}

if( !function_exists('localTrans') ) {
    /**
     * Translate the given message using local files.
     *
     * @param string|null $key
     * @param array       $replace
     * @param string|null $locale
     *
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function localTrans($key = null, $replace = [], $locale = null)
    {
        if( is_null($key) ) {
            return app('translator-local');
        }

        return app('translator-local')->get($key, $replace, $locale);
    }
}

if( !function_exists('isAdmin') ) {
    /**
     * @param \App\Models\Info\User|null $user
     *
     * @return bool
     */
    function isAdmin(User $user = null)
    {
        $user ??= auth()->user();

        return $user && $user->hasRole('admin');
    }
}
