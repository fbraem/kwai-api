<?php

namespace Domain\User;

use League\Fractal;

class UserTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'users';

    protected $defaultIncludes = [
        'rule_groups'
    ];

    public static function createForItem(User $user)
    {
        return new Fractal\Resource\Item($user, new self(), self::$type);
    }

    public static function createForCollection(iterable $users)
    {
        return new Fractal\Resource\Collection($users, new self(), self::$type);
    }

    public static function includeRuleGroups(User $user)
    {
        $rule_groups = $user->rule_groups;
        if ($rule_groups) {
            return RuleGroupTransformer::createForCollection($rule_groups);
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
