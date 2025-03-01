<?php

@include_once __DIR__ . '/utils/Plugin.php';

use Plain\Helpers\Plugin;
use Kirby\Exception\Exception;

if (option('microman.map.token')) {
    throw new Exception('Deprecation error: Option prefix microman.map changed to plain.map in config.php');
};

Plugin::load(
    'plain/map',
    ['options' => ['token' => null]],
     autoloader: ['blueprints', 'snippets', 'config', 'fields', 'translations']
);
