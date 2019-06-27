<?php

namespace Domain\User;

use League\Fractal;

class UserTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'users';

    protected $defaultIncludes = [
        'abilities'
    ];

    public static function createForItem(User $user)
    {
        return new Fractal\Resource\Item($user, new self(), self::$type);
    }

    public static function createForCollection(iterable $users)
    {
        return new Fractal\Resource\Collection($users, new self(), self::$type);
    }

    public static function includeAbilities(User $user)
    {
        $abilities = $user->abilities;
        if ($abilities) {
            return AbilitiesTransformer::createForCollection($abilities);
        }
    }

    public static function includeLogs(User $user)
    {
        $logs = $user->logs;
        if ($logs) {
            return UserLogTransformer::createForItem($logs);
        }
    }

    public function transform(User $user)
    {
        $result = $user->toArray();
        unset($result['_joinData']);
        return $result;
    }
}
