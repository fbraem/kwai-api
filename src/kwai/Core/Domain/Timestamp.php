<?php
/**
 * Class Timestamp
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain;

/**
 * Value object for a timestamp.
 */
final class Timestamp
{
    const TIMEZONE = 'UTC';

    /**
     * @var \Carbon\CarbonImmutable
     */
    private $datetime;

    /**
     * Private constructor. Use one of the create methods to create an instance
     * of this class.
     */
    private function __construct()
    {
    }

    /**
     * Returns a formatted timestamp.
     *
     * When format is null, toDateTimeString will be used.
     * @see https://carbon.nesbot.com/docs/#api-formatting String Formatting.
     *
     * @param string $format The format to use
     * @return string A formatted timestamp
     */
    public function format($format = null): string
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
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $sec
     * @param string $timezone The timezone to use. Default is UTC.
     * @return Timestamp
     */
    public static function create(
        int $year = null,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $sec = 0,
        string $timezone = self::TIMEZONE
    ): self {
        $object = new self();
        $object->datetime = \Carbon\CarbonImmutable::create(
            $year,
            $month,
            $day,
            $hour,
            $minute,
            $sec,
            $timezone
        );
        return $object;
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
        $object = new self();
        $object->datetime = \Carbon\CarbonImmutable::create(
             $datetimeObject->year ?? null,
             $datetimeObject->month ?? 1,
             $datetimeObject->day ?? 1,
             $datetimeObject->hour ?? 0,
             $datetimeObject->minute ?? 0,
             $datetimeObject->sec ?? 0,
             $datetimeObject->timezone ?? self::TIMEZONE
         );
        return $object;
    }

    /**
     * Create a Timestamp with the current time
     *
     * @param string $timezone The timezone to use. Default is UTC.
     * @return Timestamp
     */
    public static function createNow($timezone = self::TIMEZONE): self
    {
        $object = new self();
        $object->datetime = \Carbon\CarbonImmutable::now($timezone);
        return $object;
    }

    /**
     * Create a Timestamp from a string
     *
     * @param string $str A datetime in format Y-m-d H:i:s
     * @param string $timezone The timezone to use. Default is UTC.
     * @return Timestamp
     */
    public static function createFromString(
        string $str,
        string $timezone = self::TIMEZONE
    ): self {
        $object = new self();
        $object->datetime = \Carbon\CarbonImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $str,
            $timezone
        );
        return $object;
    }

    /**
     * Create from a Timestamp object from a DateTime object.
     * @param  \DateTime $time
     * @return Timestamp
     */
    public static function createFromDateTime(\DateTime $time)
    {
        $object = new self();
        $object->datetime = \Carbon\CarbonImmutable::instance($time);
        return $object;
    }
}
