<?php

namespace Domain\User;

use League\Fractal;

class UserTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'users';

    public static function createForItem(User $user)
    {
        return new Fractal\Resource\Item($user, new self(), self::$type);
    }

    public static function createForCollection(iterable $users)
    {
        return new Fractal\Resource\Collection($users, new self(), self::$type);
    }

    public function transform(User $user)
    {
        return $user->toArray();
    }
}
