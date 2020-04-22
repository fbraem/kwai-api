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
 * Class DocumentFormat
 *
 * Value object for the format of the document.
 * @method static DocumentFormat MARKDOWN()
 */
class DocumentFormat extends Enum
{
    private const MARKDOWN = 'md';
}
