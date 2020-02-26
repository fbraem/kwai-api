<?php
/**
 * @noinspection PhpUnusedPrivateFieldInspection
 * @package Kwai/Modules
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
    private const TO = 'to';
    private const BCC = 'bcc';
    private const CC = 'cc';
}
