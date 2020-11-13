<?php
/**
 * @noinspection PhpUnusedPrivateFieldInspection
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

use MyCLabs\Enum\Enum;

/**
 * Class Locale
 *
 * Value object for language
 * @method static Locale EN()
 * @method static Locale NL()
 */
class Locale extends Enum
{
    private const EN = 'en';
    private const NL = 'nl';
}
