<?php

namespace Domain\Team;

use League\Fractal;

class TeamTypeTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'team_types';

    public static function createForItem(TeamType $type)
    {
        return new Fractal\Resource\Item($type, new self(), self::$type);
    }

    public static function createForCollection(iterable $types)
    {
        return new Fractal\Resource\Collection($types, new self(), self::$type);
    }

    public function transform(TeamType $type)
    {
        return $type->toArray();
    }
}
