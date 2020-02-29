<?php
/**
 * Testcase for TokenIdentifier
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Domain;

use PHPUnit\Framework\TestCase;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

final class TokenIdentifierTest extends TestCase
{
    public function testCreateNewIdentifier(): void
    {
        $this->assertInstanceOf(
            TokenIdentifier::class,
            new TokenIdentifier()
        );
    }

    public function testIdentifierAllHex(): void
    {
        $identifier = new TokenIdentifier();
        preg_match_all('/[0-9a-fA-F]{80}/', strval($identifier), $matches);
        $this->assertIsString($matches[0][0]);
        $this->assertEquals(strlen($matches[0][0]), 80);
    }
}
