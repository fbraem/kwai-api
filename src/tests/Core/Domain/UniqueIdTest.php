<?php
/**
 * Testcase for UniqueId
 */
declare(strict_types=1);

namespace Tests\Core\Domain;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Kwai\Core\Domain\UniqueId;

final class UniqueIdTest extends TestCase
{
    public function testCreateNewUniqueId(): void
    {
        $this->assertInstanceOf(
            UniqueId::class,
            new UniqueId()
        );
    }

    public function testInvalidUniqueId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UniqueId('invalid');
    }

    public function testInvalidVersion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        // A UUID1 is passed
        new UniqueId('e4eaaaf2-d142-11e1-b3e4-080027620cdd');
    }
}
