<?php
/**
 * TokenIdentifier value object
 *
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain\ValueObjects;

use Exception;
use InvalidArgumentException;
use function bin2hex;
use function random_bytes;

/**
 * Value object for an token identifier.
 */
final class TokenIdentifier
{
    /**
     * TokenIdentifier constructor.
     *
     * @param string|null $bytes A unique byte string in hex format.
     */
    public function __construct(private ?string $bytes = null)
    {
        try {
            $this->bytes ??= bin2hex(random_bytes(40));
        } catch (Exception $e)
        {
            throw new InvalidArgumentException(message: 'Cannot create TokenIdentifier', previous: $e);
        }
    }

    public function __toString(): string
    {
        return $this->bytes;
    }
}
