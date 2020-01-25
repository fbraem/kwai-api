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
use Kwai\Modules\Users\Domain\ValueObjects\Rule;

final class AbilityMapper
{
    public static function toDomain(object $raw): Entity
    {
        $rules = [];
        foreach ($raw->rules as $rule) {
            $rules[(int) $rule->id] = new Rule(
                $rule->subject,
                $rule->action
            );
        }

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
                'rules' => $rules
            ])
        );
    }

    public static function toPersistence(Entity $ability): object
    {
    }
}
