<?php

namespace Domain\Person;

use League\Fractal;

class PersonTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'persons';

    protected $defaultIncludes = [
        'nationality',
        'contact',
        'user'
    ];

    public static function createForItem(Person $person)
    {
        return new Fractal\Resource\Item($person, new self(), self::$type);
    }

    public static function createForCollection(iterable $persons)
    {
        return new Fractal\Resource\Collection($persons, new self(), self::$type);
    }

    public function transform(Person $person)
    {
        return $person->toArray();
    }

    public function includeNationality(Person $person)
    {
        $country = $person->nationality;
        if ($country) {
            return CountryTransformer::createForItem($country);
        }
    }

    public function includeContact(Person $person)
    {
        $contact = $person->contact;
        if ($contact) {
            return ContactTransformer::createForItem($contact);
        }
    }

    public function includeUser(Person $person)
    {
        $user = $person->user;
        if ($user) {
            return $this->item($user, new \Domain\User\UserTransformer, 'users');
        }
    }
}
