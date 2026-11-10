<?php

namespace Plain\Map;

/**
 * @package   Kirby Map
 * @author    Roman Gsponer <kirby@plain-solutions.net>
 * @link      https://github.com/plain-solutions-gmbh/
 * @copyright Roman Gsponer
 * @license   MIT
 */

use Kirby\Data\Data;
use Kirby\Form\Field\InputField;

class GeolocationField extends InputField
{
    protected array $value = [];

    public function default(): mixed
    {
        return parent::default() ?? [
            'name' => '',
            'lat'  => 0,
            'lng'  => 0
        ];
    }

    public function fill(mixed $value): static
    {
        return parent::fill(Data::decode($value, 'yaml'));
    }

    public function props(): array
    {
        return [
            ...parent::props(),
            'default' => $this->default(),
            'token'   => $this->kirby()->option('plain.map.token')
        ];
    }
}
