<?php

use Kirby\Cms\App;
use Kirby\Data\Data;
use Kirby\Exception\Exception;
use Kirby\Filesystem\F;
use Plain\Map\GeolocationField;

F::loadClasses([
    'Plain\Map\GeolocationField' => __DIR__ . '/classes/GeolocationField.php',
]);

if (option('microman.map.token')) {
    throw new Exception('Deprecation error: Option prefix microman.map changed to plain.map in config.php');
};

App::plugin('plain/map', [
    'api' => require __DIR__ . '/config/api.php',
    'fieldMethods' => require __DIR__ . '/config/fieldMethods.php',
    'blueprints' => [
        'blocks/maps' => __DIR__ . '/blueprints/blocks/maps.yml',
        'blocks/marker' => __DIR__ . '/blueprints/blocks/marker.yml',
    ],
    'fields' => [
        'geolocation' => GeolocationField::class,
    ],
    'translations' => [
        'de' => Data::read(__DIR__ . '/i18n/de.json'),
        'en' => Data::read(__DIR__ . '/i18n/en.json'),
    ],
    'snippets' => [
        'blocks/maps' => __DIR__ . '/snippets/blocks/maps.php',
    ],
]);
