<?php
/**
 * Class DateTime
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain;

/**
 * Value object for a datetime.
 */
final class DateTime
{
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
     * Returns a formatted date.
     *
     * When format is null, toDateTimeString will be used.
     * @see https://carbon.nesbot.com/docs/#api-formatting String Formatting.
     *
     * @param string $format The format to use
     * @return string A formatted datetime
     */
    public function format($format = null) : string
    {
        if ($format) {
            return $this->datetime->format($format);
        }
        return $this->datetime->toDateTimeString();
    }

    /**
     * Returns a string representation of the datetime in the default format.
     *
     * @return string A formatted datetime
     */
    public function __toString() : string
    {
        return $this->format();
    }

    /**
     * Creates a new datetime object.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $sec
     * @param string $timezone The timezone to use. Default is UTC.
     */
    public static function create(
        int $year = null,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $sec = 0,
        string $timezone = 'UTC'
    ) : self {
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
     * Creates a new datetime object from an object.
     *
     * @param object $object An object containing all properties to create a new datetime object.
     */
    public static function createFromObject(object $datetimeObject) : self
    {
        $object = new self();
        $object->datetime = \Carbon\CarbonImmutable::create(
             $datetimeObject->year ?? null,
             $datetimeObject->month ?? 1,
             $datetimeObject->day ?? 1,
             $datetimeObject->hour ?? 0,
             $datetimeObject->minute ?? 0,
             $datetimeObject->sec ?? 0,
             $datetimeObject->timezone ?? 'UTC'
         );
        return $object;
    }

    /**
     * Create a datetime with the current time
     *
     * @param string $timezone The timezone to use. Default is UTC.
     */
    public static function createNow($timezone = 'UTC') : self
    {
        $object = new self();
        $object->datetime = \Carbon\CarbonImmutable::now($timezone);
        return $object;
    }
}
