<?php

use yii\caching\FileCache;
use yii\i18n\Formatter;

return [
    'language' => 'ru-RU',
    'sourceLanguage' => 'en-US',
    'charset' => 'utf-8',
    'timeZone' => 'Europe/Moscow',

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'components' => [

        'cache' => [
            'class' => FileCache::class,
        ],

        'formatter' => [
            'class' => Formatter::class,
            'locale' => 'ru-RU',
            'thousandSeparator' => ' ',
            'decimalSeparator' => '.',
            'nullDisplay' => '-',
            'currencyCode' => 'RUB',
            'defaultTimeZone' => 'Europe/Moscow',
            'timeZone' => 'Europe/Moscow',
            'dateFormat' => 'dd.MM.yyyy',
            'timeFormat' => 'H:mm:ss',
            'datetimeFormat' => 'php:d.m.y H:i',
        ],


    ],
];
