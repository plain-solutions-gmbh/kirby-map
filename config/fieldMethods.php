<?php

use Kirby\Cms\Structure;
use Kirby\Cms\StructureObject;

return [
    'toLocation' => function ($field) {
        $structure = Structure::factory([$field->yaml()], ['parent' => $field->parent()]);
        return $structure->first();
    }
];