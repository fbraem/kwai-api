<?php
/**
 * @package Pages
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Pages\Domain\Page;

class PageMapper
{
    /**
     * Maps a table record to the Page domain object.
     *
     * @param object $raw
     * @return Entity<Page>
     */
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Page((object)[
                'enabled' => $raw->enabled == '1' ?? false,
                'remark' => $raw->remark ?? null,
                'priority' => $raw->priority ?? 0,
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at)
                        ? Timestamp::createFromString($raw->updated_at)
                        : null
                ),
                'application' => ApplicationMapper::toDomain($raw->application),
                'contents' => array_map(fn($c) => new Text(
                    new Locale($c->locale),
                    new DocumentFormat($c->format),
                    $c->title,
                    $c->summary,
                    $c->content,
                    AuthorMapper::toDomain($c->author)
                ), $raw->contents)
            ])
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
        ];
    }
}
