<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Presentation\Transformers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Club\Domain\Member;
use League\Fractal;

/**
 * Class MemberTransformer
 */
class MemberTransformer extends Fractal\TransformerAbstract
{
    private static string $type = 'members';

    private function __construct()
    {
    }

    /**
     * Create a single resource of a Coach entity.
     *
     * @param Entity<Member> $member
     * @return Fractal\Resource\Item
     */
    public static function createForItem(
        Entity  $member
    ): Fractal\Resource\Item {
        return new Fractal\Resource\Item(
            $member,
            new self(),
            self::$type
        );
    }

    /**
     * Create a collection of resources for a list of coaches.
     *
     * @param iterable $members
     * @return Fractal\Resource\Collection
     */
    public static function createForCollection(
        iterable $members
    ): Fractal\Resource\Collection {
        return new Fractal\Resource\Collection(
            $members,
            new self(),
            self::$type
        );
    }

    /**
     * Transform a Member entity into an array
     *
     * @param Entity<Member> $member
     * @return array
     */
    public function transform(Entity $member): array
    {
        $traceableTime = $member->getTraceableTime();

        $result = [
            'id' => (string) $member->id(),
            'name' => (string) $member->getName(),
            'created_at' => strval($traceableTime->getCreatedAt()),
        ];
        $result['updated_at'] = $traceableTime->isUpdated()
            ? strval($traceableTime->getUpdatedAt())
            : null
        ;

        return $result;
    }
}
