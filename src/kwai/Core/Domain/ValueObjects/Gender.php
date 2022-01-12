<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

/**
 * Enum Gender
 *
 * Value object for a gender.
 */
enum Gender: int
{
    case UNKNOWN = 0;
    case MALE = 1;
    case FEMALE = 2;
}
