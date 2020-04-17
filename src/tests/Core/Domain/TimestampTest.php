<?php
/**
 * Testcase for Timestamp
 */
declare(strict_types=1);

namespace Tests\Core\Domain;

use PHPUnit\Framework\TestCase;
use Kwai\Core\Domain\ValueObjects\Timestamp;

final class TimestampTest extends TestCase
{
    public function testCreateNow(): void
    {
        $this->assertInstanceOf(
            Timestamp::class,
            Timestamp::createNow()
        );
    }

    public function testCreate(): void
    {
        $this->assertEquals(
            strval(Timestamp::create(2020, 1, 2, 10, 30, 30)),
            '2020-01-02 10:30:30'
        );
    }

    public function testCreateObject(): void
    {
        $this->assertEquals(
            strval(Timestamp::createFromObject((object) [
                'year' => 2020,
                'month' => 1,
                'day' => 31
            ])),
            '2020-01-31 00:00:00'
        );
    }

    public function testCreateObjectWithoutYear(): void
    {
        $currentYear = date('Y');
        $this->assertEquals(
            strval(Timestamp::createFromObject((object) [
                'month' => 1,
                'day' => 31
            ])),
            "$currentYear-01-31 00:00:00"
        );
    }

    public function testCreateStartOfCurrentYear(): void
    {
        $currentYear = date('Y');
        $this->assertEquals(
            strval(Timestamp::create()),
            "$currentYear-01-01 00:00:00"
        );
    }

    public function testGetTimezone(): void
    {
        $this->assertEquals(
            Timestamp::createNow()->getTimezone(),
            'UTC'
        );
    }
}
