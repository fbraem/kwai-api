<?php

namespace Domain\User;

use League\Fractal;

class RuleGroupTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'rule_groups';

    protected $defaultIncludes = [
        'rules'
    ];

    public static function createForItem(RuleGroup $group)
    {
        return new Fractal\Resource\Item($group, new self(), self::$type);
    }

    public static function createForCollection(iterable $groups)
    {
        return new Fractal\Resource\Collection($groups, new self(), self::$type);
    }

    public static function includeRules(RuleGroup $group)
    {
        $rules = $group->rules;
        if ($rules) {
            return RuleTransformer::createForCollection($rules);
        }
    }

    public function transform(RuleGroup $group)
    {
        $result = $group->toArray();
        unset($result['_joinData']);
        return $result;
    }
}
