<?php

use Kirby\Cms\App;
use Kirby\Cms\Structure;
use Kirby\Data\Yaml;

$setup = [
    'options' => [
        'defaultStyle'  => 'satellite-streets-v11',
        'token'         => null
    ],
    'blueprints' => [
        'blocks/maps' => __DIR__ . '/blueprints/blocks/maps.yml',
        'blocks/marker' => __DIR__ . '/blueprints/blocks/marker.yml',
    ],
    'fields' => [
        'geolocation' => [
            'props' => [
                'value' => function ($value = null) {
                    return Yaml::decode($value);
                },
                'token' => function () {
                    $a = option('panel');
                    return option('microman.map.token');
                }
            ]
        ]
    ],
    'fieldMethods' => [
        'toLocation' => function ($field) {
            try {
                //Kirby4 compatibility
                $structure = Structure::factory([$field->yaml()], ['parent' => $field->parent()]);
            } catch (\Throwable $th) {
                //Kirby3 compatibility
                $structure = new Structure([$field->yaml()], $field->parent());
            }
            return $structure->first();
        }
    ],
    'snippets' => [
        'blocks/maps' => __DIR__ . '/snippets/blocks/maps.php',
    ],
    'api' => [
        'routes' => [
            [
                'pattern' => 'map/options',
                'action' => function () {
                    return option('microman.map');
                }
            ]
        ]
    ],
    'translations' => [
        'en' => require __DIR__ . '/lib/languages/en.php',
        'de' => require __DIR__ . '/lib/languages/de.php'
    ]
];

//Extend downwards compatibility
if (version_compare(App::version() ?? '0.0.0', '4.9.9', '>')) {
    /** @disregard P1044 */
    App::plugin('microman/map', $setup, license: [
        'name'     => 'MIT',
        'status'    => [
            'value'     => 'missing',
            'link'      => 'https://license.microman.ch/?product=801557',
            'theme'     => 'orange',
            'label'     => 'Buy me a coffee',
            'icon'      => 'cup',

        ]
    ]); 
} else {
    App::plugin('microman/map', $setup);
}

