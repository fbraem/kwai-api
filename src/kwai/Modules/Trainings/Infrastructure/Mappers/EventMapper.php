<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Event;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Timestamp;

/**
 * Class EventMapper
 */
class EventMapper
{
    public static function toDomain(Collection $data) : Event
    {
        return new Event(
            startDate: Timestamp::createFromString($data['start_date'], $data['time_zone']),
            endDate: Timestamp::createFromString($data['end_date'], $data['time_zone']),
            location: $data->has('location') ? new Location($data->get('location')) : null,
            active: $data->get('active', '1') === '1',
            cancelled: $data->get('active', '0') === '1'
        );
    }

    public static function toPersistence(Event $event) : Collection
    {
        return collect([
            'start_date' => (string) $event->getStartDate(),
            'end_date' => (string) $event->getEndDate(),
            'time_zone' => $event->getStartDate()->getTimezone(),
            'active' => $event->isActive(),
            'cancelled' => $event->isCancelled(),
            'location' => $event->getLocation()?->__toString(),
        ]);
    }
}
