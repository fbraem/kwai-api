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

use Kwai\Modules\Users\Domain\Rule;

final class RuleMapper
{
    /**
     * Make a Rule entity from a row object
     * @param object $raw
     * @return Entity
     */
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Rule((object)[
                'name' => $raw->name,
                'action' => $raw->action,
                'subject' => $raw->subject,
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at)
                        ? Timestamp::createFromString($raw->updated_at)
                        : null
                ),
                'remark' => $raw->remark ?? ''
            ])
        );
    }

    /**
     * Persist a Rule entity to a Database row object
     * @param Entity $rule
     * @return object
     */
    public static function toPersistence(Entity $rule): object
    {
        //TODO: implement
        return (object)[];
    }
}
