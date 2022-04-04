<?php

namespace App\Nova\Packages\Fields;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\Gravatar as BaseGravatar;

class Gravatar extends BaseGravatar
{
    private $default;
    private $size;

    public function __construct($name = 'Avatar', $attribute = 'email', $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);
        static::getDefaultAvatarLink() && $this->default(static::getDefaultAvatarLink());
    }

    public static function getDefaultAvatarLink(): ?string
    {
        return config('defaults.default_avatar', config('app.default_avatar'));
    }

    protected function resolveAttribute($resource, $attribute)
    {
        $gravatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(data_get($resource, $attribute)));;

        $this->preview($callback = fn() => "$gravatar_url?" . $this->getGravatarQuery())
             ->thumbnail($callback);
    }

    public function default($url)
    {
        $this->default = $url;

        return $this;
    }

    public function size($pixels)
    {
        $this->size = $pixels;

        return $this;
    }

    private function getGravatarQuery()
    {
        $params = [];
        $params[ 'size' ] = $this->size ?? 3;

        if( $this->default ) {
            $params[ 'default' ] = $this->default;
        }

        return http_build_query($params);
    }
}
