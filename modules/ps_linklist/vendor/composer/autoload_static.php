<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticIniteb0c1e0415c565fd941ae87d9a8f458a
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PrestaShop\\Module\\LinkList\\' => 27,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PrestaShop\\Module\\LinkList\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Ps_Linklist' => __DIR__ . '/../..' . '/ps_linklist.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticIniteb0c1e0415c565fd941ae87d9a8f458a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticIniteb0c1e0415c565fd941ae87d9a8f458a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticIniteb0c1e0415c565fd941ae87d9a8f458a::$classMap;

        }, null, ClassLoader::class);
    }
}
