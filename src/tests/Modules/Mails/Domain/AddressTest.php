<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;

final class AddressTest extends TestCase
{
    public function testAddressWithEmail(): void
    {
        $address = new Address(new EmailAddress('test@kwai.com'));
        $this->assertInstanceOf(
            Address::class,
            $address
        );
        $this->assertEquals('test@kwai.com', $address->getEmail());
        $this->assertEquals('', $address->getName());
    }

    public function testAddressWithEmailAndName(): void
    {
        $address = new Address(new EmailAddress('test@kwai.com'), 'Test');
        $this->assertInstanceOf(
            Address::class,
            $address
        );
        $this->assertEquals('test@kwai.com', $address->getEmail());
        $this->assertEquals('Test', $address->getName());
    }
}