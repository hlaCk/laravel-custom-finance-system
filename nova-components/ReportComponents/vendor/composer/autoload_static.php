<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc1bc80d4d668777d50d7944973e0099a
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Sheets\\ReportComponents\\' => 24,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Sheets\\ReportComponents\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInitc1bc80d4d668777d50d7944973e0099a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc1bc80d4d668777d50d7944973e0099a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc1bc80d4d668777d50d7944973e0099a::$classMap;

        }, null, ClassLoader::class);
    }
}
