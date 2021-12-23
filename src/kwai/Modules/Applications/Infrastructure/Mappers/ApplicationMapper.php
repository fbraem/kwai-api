<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Infrastructure\Mappers;

use Illuminate\Support\Collection;
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
    public static function toDomain(Collection $data): Application
    {
        return new Application(
            title: $data->get('title'),
            description: $data->get('description'),
            shortDescription: $data->get('short_description'),
            remark: $data->get('remark'),
            name: $data->get('name'),
            traceableTime: new TraceableTime(
                Timestamp::createFromString($data->get('created_at')),
                $data->has('updated_at')
                ? Timestamp::createFromString($data->get('updated_at'))
                : null
            ),
            canHaveNews: $data->get('news', 0) === 1,
            canHavePages: $data->get('pages', 0) === 1,
            canHaveEvents: $data->get('events', 0) === 1,
            weight: (int) $data->get('weight', 0)
        );
    }

    /**
     * Returns a data collection from an Application object.
     *
     * @param Application $domain
     * @return Collection
     */
    public static function toPersistence(Application $domain): Collection
    {
        return collect([
            'title' => $domain->getTitle(),
            'description' => $domain->getDescription(),
            'short_description' => $domain->getShortDescription(),
            'remark' => $domain->getRemark(),
            'name' => $domain->getName(),
            'news' => $domain->canHaveNews(),
            'pages' => $domain->canHavePages(),
            'events' => $domain->canHaveEvents(),
            'weight' => $domain->getWeight(),
            'created_at' => $domain->getTraceableTime()->getCreatedAt(),
            'updated_at' => $domain->getTraceableTime()->getUpdatedAt(),
        ]);
    }
}
