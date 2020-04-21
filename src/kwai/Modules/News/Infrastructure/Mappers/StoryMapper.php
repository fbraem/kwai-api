<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Domain\ValueObjects\Promotion;

/**
 * Class StoryMapper
 */
class StoryMapper
{
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Story((object) [
                'enabled' => $raw->enabled ?? false,
                'promotion' => new Promotion(
                    $raw->promoted ?? false,
                    isset($raw->promoted_end_date)
                        ? Timestamp::createFromString($raw->promoted_end_date, $raw->time_zone)
                        : null
                ),
                'endDate' => isset($raw->end_date)
                    ? Timestamp::createFromString($raw->end_date, $raw->time_zone)
                    : null,
                'remark' => $raw->remark ?? null,
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at)
                        ? Timestamp::createFromString($raw->updated_at)
                        : null
                ),
                'category' => null,
                'contents' => array_map(function ($c) {
                    new Text(
                        new Locale($c->locale),
                        new DocumentFormat($c->format),
                        $c->title,
                        $c->summary,
                        $c->content,
                        AuthorMapper::toDomain($c->author)
                    );
                }, $raw->contents)
            ])
        );
    }
}
