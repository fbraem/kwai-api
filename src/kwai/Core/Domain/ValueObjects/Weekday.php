<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

use MyCLabs\Enum\Enum;

/**
 * Class Weekday
 *
 * A class representing a day in the week.
 *
 * @method static Weekday MONDAY()
 * @method static Weekday TUESDAY()
 * @method static Weekday WEDNESDAY()
 * @method static Weekday THURSDAY()
 * @method static Weekday FRIDAY()
 * @method static Weekday SATURDAY()
 * @method static Weekday SUNDAY()
 */
class Weekday extends Enum
{
    private const MONDAY = 1;
    private const TUESDAY = 2;
    private const WEDNESDAY = 3;
    private const THURSDAY = 4;
    private const FRIDAY = 5;
    private const SATURDAY = 6;
    private const SUNDAY = 7;
}
