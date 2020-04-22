<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Domain\ValueObjects\Promotion;

/**
 * Class StoryMapper
 *
 * Mapper for the news Story Entity
 */
class StoryMapper
{
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Story((object) [
                'enabled' => $raw->enabled == '1' ?? false,
                'promotion' => new Promotion(
                    $raw->promoted == '0' ?? false,
                    isset($raw->promoted_end_date)
                        ? Timestamp::createFromString(
                            $raw->promoted_end_date,
                            $raw->timezone
                        )
                        : null
                ),
                'publishTime' => Timestamp::createFromString(
                    $raw->publish_date,
                    $raw->timezone
                ),
                'endDate' => isset($raw->end_date)
                    ? Timestamp::createFromString(
                        $raw->end_date,
                        $raw->timezone
                    )
                    : null,
                'remark' => $raw->remark ?? null,
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at)
                        ? Timestamp::createFromString($raw->updated_at)
                        : null
                ),
                'category' => CategoryMapper::toDomain($raw->category),
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
}
