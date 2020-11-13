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
     * A unique bytestring in hex format.
     * @var string
     */
    private $bytes;

    public function __construct(string $hexBytes = null)
    {
        $this->bytes = $hexBytes ?? \bin2hex(\random_bytes(40));
    }

    public function __toString(): string
    {
        return $this->bytes;
    }
}
