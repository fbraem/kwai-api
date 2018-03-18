<?php

namespace Domain\Person;

use League\Fractal;

class CountryTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'countries';

    public static function createForItem(Country $country)
    {
        return new Fractal\Resource\Item($country, new self(), self::$type);
    }

    public static function createForCollection(iterable $countries)
    {
        return new Fractal\Resource\Collection($countries, new self(), self::$type);
    }

    public function transform(Country $country)
    {
        return $country->toArray();
    }
}
