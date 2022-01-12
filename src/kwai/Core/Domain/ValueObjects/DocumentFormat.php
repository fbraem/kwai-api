<?php
/**
 * @package Core
 * @subpackage Domain
 */
declare(strict_types=1);

namespace Kwai\Core\Domain\ValueObjects;

/**
 * Enum DocumentFormat
 *
 * Values the format of a document.
 */
enum DocumentFormat: string
{
    case MARKDOWN = 'md';
}
