<?php

use Kirby\Data\Yaml;

return [
    'props' => [
        'value' => function ($value = null) {
            return Yaml::decode($value);
        },
        'default' => function ($default = null) {
            return [
                'name' => '',
                'lat' => 0,
                'lng' => 0
            ];
        },
        'token' => function () {
            return option('plain.map.token');
        }
    ]
];