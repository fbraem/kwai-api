<?php
/**
 * @package Pages
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Infrastructure\Mappers\TextMapper;
use Kwai\Modules\Pages\Domain\Page;

class PageMapper
{
    /**
     * Maps a table record to the Page domain object.
     *
     * @param Collection $data
     * @return Page
     */
    public static function toDomain(Collection $data): Page
    {
        $application = $data->get('application');
        return new Page(
            enabled: $data->get('enabled', '0') === '1',
            remark: $data->get('remark'),
            priority: (int) $data->get('priority', 0),
            traceableTime: new TraceableTime(
                Timestamp::createFromString($data->get('created_at')),
                $data->has('updated_at')
                    ? Timestamp::createFromString($data->get('updated_at'))
                    : null
            ),
            application: new Entity(
            (int) $application->get('id'),
            ApplicationMapper::toDomain($application)
            ),
            contents: $data->get('contents')->map(fn($text) => TextMapper::toDomain($text))
        );
    }

    /**
     * Maps a Page domain to the table record.
     *
     * @param Page $page
     * @return array
     */
    public static function toPersistence(Page $page): array
    {
        return [
            'enabled' => $page->isEnabled(),
            'priority' => $page->getPriority(),
            'remark' => $page->getRemark(),
            'application_id' => $page->getApplication()->id(),
            'created_at' => $page->getTraceableTime()->getCreatedAt(),
            'updated_at' => $page->getTraceableTime()->getUpdatedAt()?->__toString()
        ];
    }
}
