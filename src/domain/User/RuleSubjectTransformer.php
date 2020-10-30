<?php

namespace Domain\User;

use League\Fractal;

class RuleSubjectTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'rule_subjects';

    public static function createForItem(RuleSubject $subject)
    {
        return new Fractal\Resource\Item($subject, new self(), self::$type);
    }

    public static function createForCollection(iterable $subject)
    {
        return new Fractal\Resource\Collection($subjects, new self(), self::$type);
    }

    public function transform(RuleSubject $subject)
    {
        $result = $subject->toArray();
        unset($result['_joinData']);
        return $result;
    }
}
