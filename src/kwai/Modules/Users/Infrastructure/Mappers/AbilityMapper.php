<?php
/**
 * Mapper for Ability entity.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Domain\Ability;

final class AbilityMapper
{
    /**
     * Create an Ability entity from a database row.
     *
     * @param object $raw
     * @return Entity<Ability>
     */
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Ability((object)[
                'name' => $raw->name,
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at)
                        ? Timestamp::createFromString($raw->updated_at)
                        : null
                ),
                'remark' => $raw->remark,
                'rules' => $raw->rules ?? []
            ])
        );
    }

    /**
     * Return a data array from an Ability object.
     *
     * @param Ability $ability
     * @return array
     */
    public static function toPersistence(Ability $ability): array
    {
        $updated_at = $ability->getTraceableTime()->isUpdated() ?
            strval($ability->getTraceableTime()->getUpdatedAt()) : null;

        return [
            'name' => $ability->getName(),
            'remark' => $ability->getRemark(),
            'created_at' => strval($ability->getTraceableTime()->getCreatedAt()),
            'updated_at' => $updated_at
        ];
    }
}
