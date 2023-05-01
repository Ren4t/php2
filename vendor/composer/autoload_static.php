<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2ea04e67e2b7248a06f6151c34d72bac
{
    public static $files = array (
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
        '09f6b20656683369174dd6fa83b7e5fb' => __DIR__ . '/..' . '/symfony/polyfill-uuid/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Uuid\\' => 22,
        ),
        'P' => 
        array (
            'Psr\\Container\\' => 14,
        ),
        'H' => 
        array (
            'Habr\\Renat\\' => 11,
        ),
        'F' => 
        array (
            'Faker\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Uuid\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-uuid',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Habr\\Renat\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Faker\\' => 
        array (
            0 => __DIR__ . '/..' . '/fakerphp/faker/src/Faker',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2ea04e67e2b7248a06f6151c34d72bac::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2ea04e67e2b7248a06f6151c34d72bac::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2ea04e67e2b7248a06f6151c34d72bac::$classMap;

        }, null, ClassLoader::class);
    }
}
