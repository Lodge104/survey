<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitddb1a145e450f862353420acc5153e40
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        'decc78cc4436b1292c6c0d151b19445c' => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib/bootstrap.php',
        '3109cb1a231dcd04bee1f9f620d46975' => __DIR__ . '/..' . '/paragonie/sodium_compat/autoload.php',
        '56823cacd97af379eceaf82ad00b928f' => __DIR__ . '/..' . '/phpseclib/bcmath_compat/lib/bcmath.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'p' => 
        array (
            'phpseclib3\\' => 11,
        ),
        'b' => 
        array (
            'bcmath_compat\\' => 14,
        ),
        'T' => 
        array (
            'Twig\\' => 5,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'SPSS\\' => 5,
        ),
        'P' => 
        array (
            'ParagonIE\\ConstantTime\\' => 23,
        ),
        'L' => 
        array (
            'LimeSurvey\\PluginManager\\' => 25,
            'LimeSurvey\\Models\\Services\\' => 27,
            'LimeSurvey\\Menu\\' => 16,
            'LimeSurvey\\Helpers\\Update\\' => 26,
            'LimeSurvey\\Helpers\\' => 19,
            'LimeSurvey\\ExtensionInstaller\\' => 30,
            'LimeSurvey\\Exceptions\\' => 22,
            'LimeSurvey\\Datavalueobjects\\' => 28,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'phpseclib3\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
        ),
        'bcmath_compat\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpseclib/bcmath_compat/src',
        ),
        'Twig\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'SPSS\\' => 
        array (
            0 => __DIR__ . '/..' . '/tiamo/spss/src',
        ),
        'ParagonIE\\ConstantTime\\' => 
        array (
            0 => __DIR__ . '/..' . '/paragonie/constant_time_encoding/src',
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
        'LimeSurvey\\Helpers\\Update\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application/helpers/update',
            1 => __DIR__ . '/../..' . '/application/helpers/update/updates',
        ),
        'LimeSurvey\\Helpers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application/helpers',
        ),
        'LimeSurvey\\ExtensionInstaller\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application/libraries/ExtensionInstaller',
        ),
        'LimeSurvey\\Exceptions\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application/exceptions',
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
        'Yii' => __DIR__ . '/..' . '/yiisoft/yii/framework/yii.php',
        'YiiBase' => __DIR__ . '/..' . '/yiisoft/yii/framework/YiiBase.php',
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
