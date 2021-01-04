<?php
/**
 * Mapper for Ability entity.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Domain\Rule;

final class RuleMapper
{
    /**
     * Make a Rule entity from a row object
     *
     * @param Collection $data
     * @return Rule
     */
    public static function toDomain(Collection $data): Rule
    {
        return new Rule(
            name: $data->get('name'),
            action: $data->get('action'),
            subject: $data->get('subject'),
            traceableTime: new TraceableTime(
                Timestamp::createFromString($data->get('created_at')),
                $data->has('updated_at')
                    ? Timestamp::createFromString($data->get('updated_at'))
                    : null
            ),
            remark: $data->get('remark')
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
