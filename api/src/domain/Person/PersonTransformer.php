<?php

namespace Domain\Person;

use League\Fractal;

class PersonTransformer extends Fractal\TransformerAbstract
{
    protected $defaultIncludes = [
        'nationality',
        'contact',
        'user'
    ];

    public function transform(PersonInterface $person)
    {
        return $person->extract();
    }

    public function includeNationality(PersonInterface $person)
    {
        $country = $person->nationality();
        if ($country) {
            return $this->item($country, new CountryTransformer, 'countries');
        }
    }

    public function includeContact(PersonInterface $person)
    {
        $contact = $person->contact();
        if ($contact) {
            return $this->item($contact, new ContactTransformer, 'contacts');
        }
    }

    public function includeUser(PersonInterface $person)
    {
        $user = $person->user();
        if ($user) {
            return $this->item($user, new \Domain\User\UserTransformer, 'users');
        }
    }
}
