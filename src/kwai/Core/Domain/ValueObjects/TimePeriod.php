<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Class TimePeriod
 *
 * Represents a period between two time values.
 */
class TimePeriod
{
    public function __construct(
        private Time $start,
        private Time $end
    ) {
        if ($end->isBefore($start)) {
            throw new InvalidArgumentException('Start time must be before end time');
        }
    }

    /**
     * Get the start time
     * @return Time
     */
    public function getStart(): Time
    {
        return $this->start;
    }

    /**
     * Get the end time
     *
     * @return Time
     */
    public function getEnd(): Time
    {
        return $this->end;
    }
}
