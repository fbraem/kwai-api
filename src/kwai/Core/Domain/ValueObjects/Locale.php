<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;


/**
 * Class Locale
 *
 * Value object for language
 */
enum Locale: string
{
    case EN = 'en';
    case NL = 'nl';
}
