<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

use InvalidArgumentException;

class Weekday
{
    private int $day;

    /**
     * Weekday constructor.
     *
     * @param int $day
     */
    public function __construct(int $day)
    {
        if ($day < 1 || $day > 7) {
            throw new InvalidArgumentException("$day must be between 1 and 7");
        }
        $this->day = $day;
    }

    /**
     * @return int
     */
    public function getDay(): int
    {
        return $this->day;
    }
}
