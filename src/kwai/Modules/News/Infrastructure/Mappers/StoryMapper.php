<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Infrastructure\Mappers\TextMapper;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Domain\ValueObjects\Promotion;

/**
 * Class StoryMapper
 *
 * Mapper for the news Story Entity
 */
class StoryMapper
{
    public static function toDomain(Collection $data): Story
    {
        return new Story(
            enabled: $data->get('enabled', '0') === '1',
            promotion: new Promotion(
                (int) $data->get('promotion', 0),
                $data->has('promotion_end_date')
                    ? Timestamp::createFromString(
                        $data->get('promotion_end_date'),
                        $data->get('timezone')
                    )
                    : null
            ),
            publishTime: Timestamp::createFromString(
                $data->get('publish_date'),
                $data->get('timezone')
            ),
            endDate: $data->has('end_date')
                ? Timestamp::createFromString(
                    $data->get('end_date'),
                    $data->get('timezone')
                )
                : null,
            remark: $data->get('remark'),
            traceableTime: new TraceableTime(
                Timestamp::createFromString($data->get('created_at')),
                $data->has('updated_at')
                            ? Timestamp::createFromString($data->get('updated_at'))
                            : null
            ),
            application: new Entity(
                (int) $data->get('application')->get('id'),
                ApplicationMapper::toDomain($data->get('application'))
            ),
            contents: $data->get('contents')->map(fn ($text) => TextMapper::toDomain($text))
        );
    }

    /**
     * Returns a data array from a Story object.
     *
     * @param Story $story
     * @return Collection
     */
    public static function toPersistence(Story $story): Collection
    {
        return collect([
            'enabled' => $story->isEnabled(),
            'promotion' => $story->getPromotion()->getPriority(),
            'promotion_end_date' => $story->getPromotion()->getEndDate()?->__toString(),
            'publish_date'=> $story->getPublishTime(),
            'timezone' => $story->getPublishTime()->getTimezone(),
            'end_date' => $story->getEndDate()?->__toString(),
            'application_id' => $story->getApplication()->id(),
            'remark' => $story->getRemark(),
            'created_at'=> $story->getTraceableTime()->getCreatedAt(),
            'updated_at'=> $story->getTraceableTime()->getUpdatedAt()?->__toString()
        ]);
    }
}
