<?php

namespace Domain\User;

use League\Fractal;

class RuleTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'rules';

    protected $defaultIncludes = [
        'action',
        'subject'
    ];

    public static function createForItem(Rule $rule)
    {
        return new Fractal\Resource\Item($rule, new self(), self::$type);
    }

    public static function createForCollection(iterable $rules)
    {
        return new Fractal\Resource\Collection($rules, new self(), self::$type);
    }

    public static function includeAction(Rule $rule)
    {
        $action = $rule->action;
        if ($action) {
            return RuleActionTransformer::createForItem($action);
        }
    }

    public static function includeSubject(Rule $rule)
    {
        $subject = $rule->subject;
        if ($subject) {
            return RuleSubjectTransformer::createForItem($subject);
        }
    }

    public function transform(Rule $rule)
    {
        $result = $rule->toArray();
        unset($result['_joinData']);
        return $result;
    }
}
