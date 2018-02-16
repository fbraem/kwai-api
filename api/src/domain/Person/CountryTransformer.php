<?php

namespace Domain\Person;

use League\Fractal;

class CountryTransformer extends Fractal\TransformerAbstract
{
    public function transform(CountryInterface $country)
    {
        return $country->extract();
    }
}
