<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

use InvalidArgumentException;

/**
 * Class Time
 *
 * A value object for a time in a day (HH:MM)
 */
class Time
{
    private int $hour;

    private int $minute;

    private string $timezone;

    /**
     * Time constructor.
     *
     * @param int    $hour
     * @param int    $minute
     * @param string $timezone
     */
    public function __construct(int $hour, int $minute, string $timezone)
    {
        if ($hour < 0 || $hour > 23) {
            throw new InvalidArgumentException("$hour is not a valid hour");
        }
        $this->hour = $hour;
        if ($minute < 0 || $minute > 59) {
            throw new InvalidArgumentException("$minute is not a valid minute");
        }
        $this->minute = $minute;
        $this->timezone = $timezone;
    }

    /**
     * @return int
     */
    public function getHour(): int
    {
        return $this->hour;
    }

    /**
     * @return int
     */
    public function getMinute(): int
    {
        return $this->minute;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * Is this time before the given time?
     *
     * @param Time $time
     * @return bool
     */
    public function isBefore(Time $time): bool
    {
        if ($this->hour < $time->hour) {
            return true;
        }
        if ($this->hour == $time->hour) {
            return $this->minute < $time->minute;
        }
        return false;
    }

    /**
     * Create a Time object from a string
     *
     * @param string $time
     * @param string $timezone
     * @return Time
     */
    public static function createFromString(string $time, string $timezone)
    {
        [$hour, $minute] = explode(':', $time);
        return new self(
            (int) $hour,
            (int) $minute,
            $timezone
        );
    }

    /**
     * Return the time formatted as HH:MM
     *
     * @return string
     */
    public function format(): string
    {
        return sprintf('%02d:%02d', $this->hour, $this->minute);
    }

    /**
     * Returns a string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->format();
    }
}
