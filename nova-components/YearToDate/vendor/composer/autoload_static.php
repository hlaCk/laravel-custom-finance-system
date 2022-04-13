<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit454830c95461f8e1cb356023ed3fdf7c
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Sheets\\YearToDate\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Sheets\\YearToDate\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit454830c95461f8e1cb356023ed3fdf7c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit454830c95461f8e1cb356023ed3fdf7c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit454830c95461f8e1cb356023ed3fdf7c::$classMap;

        }, null, ClassLoader::class);
    }
}
