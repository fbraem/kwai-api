<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Domain\ValueObjects;

/**
 * Value object for a recipient type.
 */
enum RecipientType: int
{
    case TO = 1;
    case BCC = 2;
    case CC = 3;
}
