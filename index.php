<?php

use Kirby\Text\Markdown;

Kirby::plugin('microman/map', [
    'options' => array(
		'token'         => 'pk.eyJ1IjoibWljcm9tYW4iLCJhIjoiY2txOWg0ZDg2MDJqdDJxbW9sMGNhbjFwaCJ9.j7h8Wv0LnS2QqmuL7VR6wQ',
        'defaultStyle'  => 'streets-v11',
	),
    'blueprints' => [
        'blocks/maps' => __DIR__ . '/blueprints/blocks/maps.yml',
        'blocks/marker' => __DIR__ . '/blueprints/blocks/marker.yml',
    ],
    'icons' => [],
    'fields' => [
        'geolocation' => [
            'props' => [
                'token' => function() {
                    return option('microman.map.token');
                }
            ]
        ]
    ],
    'fieldMethods' => array(
        'toLocation' => function ($field) {
            $structure = new Structure([$field->yaml()], $field->parent());
            return $structure->first();
        }
    ),
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
            ],
            [
                'pattern' => 'map/converter',
                'method' => 'POST',
                'action' => function () {
                    return array('html' => kt(get('markdown')));
                }
            ]
        ]
            ],
    'translations' => array(
        'en' => require_once __DIR__ . '/lib/languages/en.php',
        'de' => require_once __DIR__ . '/lib/languages/de.php'
    ),

]);
