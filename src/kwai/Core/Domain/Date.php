<?php
/**
 * Class Date
 */
declare(strict_types = 1);

namespace Kwai\Core\Domain;

/**
 * Value object for a date.
 */
final class Date
{
    /**
     * @var \Carbon\CarbonImmutable
     */
    private $date;

    /**
     * Constructs a new Date value object.
     *
     * @param int $year The year of the date
     * @param int $month The month of the date
     * @param int $day The day of the date
     */
    public function __construct(int $year = null, int $month = 1, int $day = 1)
    {
        if ($year) {
            $this->date = \Carbon\CarbonImmutable::createFromDate($year, $month, $day);
        } else {
            $this->date = \Carbon\CarbonImmutable::today();
        }
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
}
