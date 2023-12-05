<?php

\Kirby\Cms\App::plugin('microman/map', [
    'options' => [
        'token'         => 'pk.eyJ1IjoibWljcm9tYW4iLCJhIjoiY2txOWg0ZDg2MDJqdDJxbW9sMGNhbjFwaCJ9.j7h8Wv0LnS2QqmuL7VR6wQ',
        'defaultStyle'  => 'streets-v11',
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
]);
