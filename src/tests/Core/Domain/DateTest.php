<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

namespace Tests\Core\Domain;

use PHPUnit\Framework\TestCase;
use Kwai\Core\Domain\Date;

final class DateTest extends TestCase
{
    public function testCreateValidDate(): void
    {
        $this->assertInstanceOf(
            Date::class,
            new Date()
        );
    }

    public function testCreateStartOfYear(): void
    {
        $this->assertEquals(
            strval(new Date(2019)),
            '2019-01-01'
        );
    }

    public function testCreateStartOfMonth(): void
    {
        $this->assertEquals(
            strval(new Date(2019, 2)),
            '2019-02-01'
        );
    }

    public function testCreateDate(): void
    {
        $this->assertEquals(
            strval(new Date(2019, 2, 28)),
            '2019-02-28'
        );
    }
}
