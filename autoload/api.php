<?php 

return [
    'routes' => [
        [
            'pattern' => 'map/options',
            'action' => function () {
                return option('plain.map');
            }
        ]
    ]
];