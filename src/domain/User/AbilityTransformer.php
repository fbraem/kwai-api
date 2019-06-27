<?php

namespace Domain\User;

use League\Fractal;

class AbilityTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'abilities';

    protected $defaultIncludes = [
        'rules'
    ];

    public static function createForItem(Ability $ability)
    {
        return new Fractal\Resource\Item($ability, new self(), self::$type);
    }

    public static function createForCollection(iterable $abilities)
    {
        return new Fractal\Resource\Collection($abilities, new self(), self::$type);
    }

    public static function includeRules(Ability $ability)
    {
        $rules = $ability->rules;
        if ($rules) {
            return RuleTransformer::createForCollection($rules);
        }
    }

    public function transform(Ability $ability)
    {
        $result = $ability->toArray();
        unset($result['_joinData']);
        return $result;
    }
}
