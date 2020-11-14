<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3e8f95d9b4b1dbdd6f7eb126a99fd4d2
{
    public static $prefixLengthsPsr4 = array (
        'O' => 
        array (
            'OnBoarding\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'OnBoarding\\' => 
        array (
            0 => __DIR__ . '/../..' . '/OnBoarding',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3e8f95d9b4b1dbdd6f7eb126a99fd4d2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3e8f95d9b4b1dbdd6f7eb126a99fd4d2::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
