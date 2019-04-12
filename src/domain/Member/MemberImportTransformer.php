<?php

namespace Domain\Member;

use League\Fractal;

class MemberImportTransformer extends Fractal\TransformerAbstract
{
    private static $type = 'member_imports';

    private $converter = null;

    public static function createForItem(MemberImport $import)
    {
        return new Fractal\Resource\Item($import, new self(), self::$type);
    }

    public static function createForCollection(iterable $imports)
    {
        return new Fractal\Resource\Collection($imports, new self(), self::$type);
    }

    protected $defaultIncludes = [
        'user'
    ];

    public function __construct()
    {
    }

    public function transform(MemberImport $memberImport)
    {
        $result = $memberImport->toArray();
        unset($result['_matchingData']);

        return $result;
    }

    public function includeUser(MemberImport $memberImport)
    {
        $user = $memberImport->user;
        if ($user) {
            return \Domain\User\UserTransformer::createForItem($memberImport);
        }
    }
}
