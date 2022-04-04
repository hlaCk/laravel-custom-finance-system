<?php

namespace App\Traits;

use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait TInteractsWithMedia
{
    use InteractsWithMedia;

// region: collections-conversions
    public static array $media_names = [
        'hero' => 'hero_image',
        'video' => 'video',
        'videos' => 'videos',
        'image' => 'image',
        'images' => 'images',
        'silks' => 'silk',
        'thumb' => 'thumb',
        'thumbs' => 'thumbs',
        'pdf' => 'pdf',
        'page_image' => 'page_image',
    ];

    public array $mediaCollectionsGroup = [
        'hero' => 'single',
        'video' => 'single',
        'videos' => 'multi',
        'image' => 'single',
        'images' => 'multi',
        'thumb' => 'single',
        'thumbs' => 'multi',
        'pdf' => 'single',
        'page_image' => 'single',
    ];

    public static array $conversionNames = [
        'hero' => 'app.media_collection_hero_image_name',

        'page_image' => 'app.media_collection_single_page_image',

        'image' => 'app.media_collection_single_image_name',
        'images' => 'app.media_collection_multiple_image_name',

        'video' => 'app.media_collection_single_video_name',
        'videos' => 'app.media_collection_multiple_video_name',

        'thumb' => 'app.media_collection_thumb',
        'full' => 'app.media_collection_full-size',
    ];

    public static string $defaultConversionConfigName = 'app.media_collection_name';

    /**
     * @param string|int $name
     * @param string|int $default
     *
     * @return array|string|null|mixed
     */
    public static function getMediaName($name, $default = 'image')
    {
        return data_get(static::$media_names, $name, $default);
    }

    public static function getDefaultConversionName(string $default = ''): string
    {
        return (string) (config(static::$defaultConversionConfigName ?? 'app.media_collection_name') ?: $default);
    }

    /**
     * @param int|string    $index values: hero,image,images,video,videos,thumb,full-size
     * @param callable|null $callback
     *
     * @return string|array|null|mixed
     */
    public static function getConversionName($index = 0, callable $callback = null)
    {
        $callback = $callback ?? fn($x) => $x;
        $defaultConversion = static::getDefaultConversionName();
        $conversionNames = (array) (static::$conversionNames ?? $defaultConversion);
        $index = is_int($index) ? $index : trim(snake_case($index));
        $conversion = data_get($conversionNames, $index, $defaultConversion);

        return $callback(config($conversion, $conversion));
    }

    public function isSingleMedia(string $name): bool
    {
        return ($this->mediaCollectionsGroup[ $name ] ?? 'single') === 'single';
    }

    public function isMultiMedia(string $name): bool
    {
        return ($this->mediaCollectionsGroup[ $name ] ?? 'single') === 'multi';
    }

// endregion: collections-conversions

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion(static::getConversionName('thumb'))
             ->width(368)
             ->height(232)
             ->extractVideoFrameAtSecond(1);

        $this->addMediaConversion(static::getConversionName('full'));
    }

    public function registerMediaCollections(): void
    {
        $this->registerMediaCollection(static::getMediaName('hero'));
        $this->registerMediaCollection(static::getMediaName('video'));
        $this->registerMediaCollection(static::getMediaName('videos'));
        $this->registerMediaCollection(static::getMediaName('image'));
        $this->registerMediaCollection(static::getMediaName('images'));
        $this->registerMediaCollection(static::getMediaName('pdf'));
        $this->registerMediaCollection(static::getMediaName('thumb'));
        $this->registerMediaCollection(static::getMediaName('thumbs'));
        $this->registerMediaCollection(static::getMediaName('page_image'));
    }

    public function registerMediaCollection(string $name, bool $skipCollectionsGroup = false): MediaCollection
    {
        $mediaCollection = $this->addMediaCollection($name);
        if( !$skipCollectionsGroup ) {
            if( $this->isSingleMedia($name) ) {
                $mediaCollection = $mediaCollection->singleFile();
            } elseif( $this->isMultiMedia($name) ) {
                $mediaCollection->collectionSizeLimit = false;
                $mediaCollection->singleFile = false;
            }
        }

        return $mediaCollection;
    }

}
