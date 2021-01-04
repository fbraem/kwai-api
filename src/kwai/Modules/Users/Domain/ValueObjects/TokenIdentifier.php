<?php
/**
 * TokenIdentifier value object
 *
 * @package Kwai/Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Domain\ValueObjects;

/**
 * Value object for an token identifier.
 */
final class TokenIdentifier
{
    /**
     * TokenIdentifier constructor.
     *
     * @param string|null $bytes A unique byte string in hex format.
     * @throws \Exception
     */
    public function __construct(private ?string $bytes = null)
    {
        $this->bytes ??= \bin2hex(\random_bytes(40));
    }

    public function __toString(): string
    {
        return $this->bytes;
    }
}
