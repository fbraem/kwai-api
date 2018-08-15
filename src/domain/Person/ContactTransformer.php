<?php

namespace Domain\Person;

use League\Fractal;

class ContactTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'contacts';

    protected $defaultIncludes = [
        'country'
    ];

    public static function createForItem(Contact $contact)
    {
        return new Fractal\Resource\Item($contact, new self(), self::$type);
    }

    public static function createForCollection(iterable $contacts)
    {
        return new Fractal\Resource\Collection($contacts, new self(), self::$type);
    }

    public function transform(Contact $contact)
    {
        return $contact->toArray();
    }

    public function includeCountry(Contact $contact)
    {
        $country = $contact->country;
        if ($country) {
            return CountryTransformer::createForItem($country);
        }
    }
}
