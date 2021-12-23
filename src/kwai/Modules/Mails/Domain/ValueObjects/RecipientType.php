<?php
/**
 * @noinspection PhpUnusedPrivateFieldInspection
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Domain\ValueObjects;

use MyCLabs\Enum\Enum;

/**
 * Value object for a recipient type.
 * @method static RecipientType TO()
 * @method static RecipientType BCC()
 * @method static RecipientType CC()
 */
final class RecipientType extends Enum
{
    private const TO = 1;
    private const BCC = 2;
    private const CC = 3;
}
