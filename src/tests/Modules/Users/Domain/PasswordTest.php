<?php
/**
 * Testcase for Password
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Kwai\Modules\Users\Domain\ValueObjects\Password;

final class PasswordTest extends TestCase
{
    public function testPasswordFromString(): string
    {
        $password = Password::fromString('hajime');
        $this->assertInstanceOf(
            Password::class,
            $password
        );
        return strval($password);
    }

    /**
     * @depends testPasswordFromString
     */
    public function testPassword(string $hashedPassord): void
    {
        $password = new Password($hashedPassord);
        $this->assertTrue($password->verify('hajime'));
    }
}
