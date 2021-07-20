<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitddb1a145e450f862353420acc5153e40
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '3109cb1a231dcd04bee1f9f620d46975' => __DIR__ . '/..' . '/paragonie/sodium_compat/autoload.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twig\\' => 5,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Ctype\\' => 23,
        ),
        'L' => 
        array (
            'LimeSurvey\\PluginManager\\' => 25,
            'LimeSurvey\\Models\\Services\\' => 27,
            'LimeSurvey\\Menu\\' => 16,
            'LimeSurvey\\Helpers\\' => 19,
            'LimeSurvey\\ExtensionInstaller\\' => 30,
            'LimeSurvey\\Datavalueobjects\\' => 28,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twig\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'LimeSurvey\\PluginManager\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application/libraries/PluginManager',
            1 => __DIR__ . '/../..' . '/application/libraries/PluginManager/Storage',
        ),
        'LimeSurvey\\Models\\Services\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application/models/services',
        ),
        'LimeSurvey\\Menu\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application/libraries/MenuObjects',
        ),
        'LimeSurvey\\Helpers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application/helpers',
        ),
        'LimeSurvey\\ExtensionInstaller\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application/libraries/ExtensionInstaller',
        ),
        'LimeSurvey\\Datavalueobjects\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application/datavalueobjects',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitddb1a145e450f862353420acc5153e40::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitddb1a145e450f862353420acc5153e40::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitddb1a145e450f862353420acc5153e40::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitddb1a145e450f862353420acc5153e40::$classMap;

        }, null, ClassLoader::class);
    }
}
