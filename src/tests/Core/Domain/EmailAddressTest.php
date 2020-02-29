<?php
/**
 * Testcase for EmailAddress
 */
declare(strict_types=1);

namespace Tests\Core\Domain;

use PHPUnit\Framework\TestCase;
use Kwai\Core\Domain\EmailAddress;

final class EmailAddressTest extends TestCase
{
    public function testCreateValidEmailAddress(): void
    {
        $this->assertInstanceOf(
            EmailAddress::class,
            new EmailAddress('user@example.com')
        );
    }

    public function testInvalidEmailAddress(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new EmailAddress('invalid');
    }

    public function testEmailAddressToString(): void
    {
        $this->assertEquals(
            strval(new EmailAddress('user@example.com')),
            'user@example.com'
        );
    }
}
