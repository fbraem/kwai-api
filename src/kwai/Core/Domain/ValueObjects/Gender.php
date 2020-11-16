<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

use MyCLabs\Enum\Enum;

/**
 * Class Gender
 *
 * Value object for a gender.
 * @method static Gender UNKNOWN()
 * @method static Gender MALE()
 * @method static Gender FEMALE()
 */
final class Gender extends Enum
{
    private const UNKNOWN = 0;
    private const MALE = 1;
    private const FEMALE = 2;
}
