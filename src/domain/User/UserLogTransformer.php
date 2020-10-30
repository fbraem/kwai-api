<?php

namespace Domain\User;

use League\Fractal;

class UserLogTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'user_logs';

    protected $defaultIncludes = [
        'user'
    ];

    public static function createForItem(UserLog $log)
    {
        return new Fractal\Resource\Item($log, new self(), self::$type);
    }

    public static function createForCollection(iterable $logs)
    {
        return new Fractal\Resource\Collection($logs, new self(), self::$type);
    }

    public static function includeRules(UserLog $log)
    {
        $user = $log->user;
        if ($rules) {
            return UserTransformer::createForItem($user);
        }
    }

    public function transform(UserLog $log)
    {
        $result = $log->toArray();
        unset($result['_joinData']);
        return $result;
    }
}
