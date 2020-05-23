<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Applications\Domain\Application;

/**
 * Class ApplicationMapper
 *
 * Mapper for the news Application entity.
 */
class ApplicationMapper
{
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Application((object)[
                'name' => $raw->name,
                'description' => $raw->description,
                'shortDescription' => $raw->short_description,
                'remark' => $raw->remark ?? null,
                'application' => $raw->app,
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at)
                        ? Timestamp::createFromString($raw->updated_at)
                        : null
                ),
                'canHaveNews' => $raw->news == '1' ?? false,
                'canHavePages' => $raw->pages == '1' ?? false,
                'canHaveEvents' => $raw->events == '1' ?? false,
            ])
        );
    }
}
