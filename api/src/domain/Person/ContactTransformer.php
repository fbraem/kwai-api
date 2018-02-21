<?php

namespace Domain\Person;

use League\Fractal;

class ContactTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'country'
    ];

    public function transform(ContactInterface $contact)
    {
        return $contact->extract();
    }

    public function includeCountry(ContactInterface $contact)
    {
        $country = $contact->country();
        if ($country) {
            return $this->item($country, new CountryTransformer, 'countries');
        }
    }
}
