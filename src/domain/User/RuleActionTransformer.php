<?php

namespace Domain\User;

use League\Fractal;

class RuleActionTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'rule_actions';

    public static function createForItem(RuleAction $action)
    {
        return new Fractal\Resource\Item($action, new self(), self::$type);
    }

    public static function createForCollection(iterable $actions)
    {
        return new Fractal\Resource\Collection($actions, new self(), self::$type);
    }

    public function transform(RuleAction $action)
    {
        $result = $action->toArray();
        unset($result['_joinData']);
        return $result;
    }
}
