<?php
/**
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;

use Kwai\Modules\Users\Domain\Ability;

final class AbilityMapper
{
    /**
     * Create an Ability entity from a database row.
     *
     * @param Collection $data
     * @return Ability
     */
    public static function toDomain(Collection $data): Ability
    {
        return new Ability(
            name: $data->get('name'),
            traceableTime: new TraceableTime(
                Timestamp::createFromString($data->get('created_at')),
                $data->has('updated_at')
                    ? Timestamp::createFromString($data->get('updated_at'))
                    : null
            ),
            remark: $data->get('remark'),
            rules: $data->get('rules')
        );
    }

    /**
     * Return a data array from an Ability object.
     *
     * @param Ability $ability
     * @return Collection
     */
    public static function toPersistence(Ability $ability): Collection
    {
        return collect([
            'name' => $ability->getName(),
            'remark' => $ability->getRemark(),
            'created_at' => strval($ability->getTraceableTime()->getCreatedAt()),
            'updated_at' => $ability->getTraceableTime()->getUpdatedAt()?->__toString()
        ]);
    }
}
