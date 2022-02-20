<?php
/**
 * Class Timestamp
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain\ValueObjects;

use Carbon\CarbonImmutable;
use DateTime;

/**
 * Value object for a timestamp.
 */
final class Timestamp
{
    /**
     * Private constructor. Use one of the create methods to create an instance
     * of this class.
     *
     * @param CarbonImmutable $datetime
     */
    private function __construct(
        private CarbonImmutable $datetime
    ) {
        $this->datetime = $this->datetime->setTimezone('UTC');
    }

    /**
     * Returns the difference in days.
     * @param Timestamp|null $timestamp
     * @return int
     */
    public function diffInDays(Timestamp $timestamp = null): int
    {
        if ($timestamp == null) {
            $timestamp = Timestamp::createNow();
        }
        return $this->datetime->diffInDays($timestamp->datetime);
    }

    /**
     * Returns a formatted timestamp.
     *
     * When format is null, toDateTimeString will be used.
     *
     * @see https://carbon.nesbot.com/docs/#api-formatting String Formatting.
     *
     * @param string|null $format The format to use
     * @return string A formatted timestamp
     */
    public function format(?string $format = null): string
    {
        if ($format) {
            return $this->datetime->format($format);
        }
        return $this->datetime->toDateTimeString();
    }

    /**
     * Get the timezone
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->datetime->tzName;
    }

    /**
     * Returns true when the timestamp is in the past.
     * @return bool
     */
    public function isPast(): bool
    {
        return $this->datetime->isPast();
    }

    /**
     * Returns a string representation of the timestamp in the default format.
     *
     * @return string A formatted timestamp
     */
    public function __toString(): string
    {
        return $this->format();
    }

    /**
     * Creates a new Timestamp object.
     *
     * @param int|null $year
     * @param int      $month
     * @param int      $day
     * @param int      $hour
     * @param int      $minute
     * @param int      $sec
     * @return Timestamp
     */
    public static function create(
        ?int $year = null,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $sec = 0
    ): self {
        return new self(CarbonImmutable::create(
            $year,
            $month,
            $day,
            $hour,
            $minute,
            $sec
        ));
    }

    /**
     * Creates a new Timestamp object from an object.
     *
     * @param object $datetimeObject An object containing all properties to
     *                               create a new Timestamp object.
     * @return Timestamp
     */
    public static function createFromObject(object $datetimeObject): self
    {
        return new self(CarbonImmutable::create(
            $datetimeObject->year ?? null,
            $datetimeObject->month ?? 1,
            $datetimeObject->day ?? 1,
            $datetimeObject->hour ?? 0,
            $datetimeObject->minute ?? 0,
            $datetimeObject->sec ?? 0,
            $datetimeObject->timezone ?? 'UTC'
        ));
    }

    /**
     * Create a Timestamp with the current time
     *
     * @return Timestamp
     */
    public static function createNow(): self
    {
        return new self(CarbonImmutable::now());
    }

    /**
     * Create a Timestamp from a string. The datetime will always be stored
     * as UTC.
     *
     * @param string $str A datetime in format Y-m-d H:i:s
     * @param string $timezone
     * @return Timestamp
     */
    public static function createFromString(
        string $str,
        string $timezone = 'UTC'
    ): self {
        return new self(CarbonImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $str,
            $timezone
        ));
    }

    /**
     * Create from a Timestamp object from a DateTime object.
     * @param  DateTime $time
     * @return Timestamp
     */
    public static function createFromDateTime(DateTime $time): self
    {
        return new self(CarbonImmutable::instance($time));
    }
}
