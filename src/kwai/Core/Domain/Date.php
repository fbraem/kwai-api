<?php
/**
 * Class Date
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain;

use Carbon\CarbonImmutable;

/**
 * Value object for a date.
 */
final class Date
{
    private CarbonImmutable $date;

    /**
     * Constructs a new Date value object.
     *
     * @param CarbonImmutable $date
     */
    private function __construct(CarbonImmutable $date)
    {
        $this->date = $date;
    }

    /**
     * Returns a formatted date.
     * The default format is Y-m-d.
     * @see https://www.php.net/manual/en/datetime.format.php Date formats.
     *
     * @param string $format The format to use
     * @return string A formatted date
     */
    public function format($format = 'Y-m-d') : string
    {
        return $this->date->format($format);
    }

    /**
     * Returns a string representation of the date in the default format.
     *
     * @return string A formatted date
     */
    public function __toString() : string
    {
        return $this->format();
    }

    /**
     * Creates a date with the given year (current year when omitted), given month (january when omitted)
     * and given day (first day when omitted).
     *
     * @param int|null $year
     * @param int $month
     * @param int $day
     * @return Date
     */
    public static function createFromDate(int $year = null, int $month = 1, int $day = 1): Date
    {
        return new Date(CarbonImmutable::createFromDate($year, $month, $day));
    }

    /**
     * Creates a date for today.
     * @return Date
     */
    public static function createToDay(): Date
    {
        return new Date(CarbonImmutable::today());
    }

    /**
     * Creates a date in the future by adding days to the current day.
     * @param int $days
     * @return Date
     */
    public static function createFuture(int $days): Date
    {
        return new Date(CarbonImmutable::today()->addDays($days));
    }
}
