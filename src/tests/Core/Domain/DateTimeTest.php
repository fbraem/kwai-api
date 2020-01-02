<?php
/**
 * Testcase for DateTime
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Kwai\Core\Domain\DateTime;

final class DateTimeTest extends TestCase
{
    public function testCreateNow(): void
    {
        $this->assertInstanceOf(
            DateTime::class,
            DateTime::createNow()
        );
    }

    public function testCreate(): void
    {
        $this->assertEquals(
            strval(DateTime::create(2020, 1, 2, 10, 30, 30)),
            '2020-01-02 10:30:30'
        );
    }

    public function testCreateObject(): void
    {
        $this->assertEquals(
            strval(DateTime::createFromObject((object) [
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
            strval(DateTime::createFromObject((object) [
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
            strval(DateTime::create()),
            "$currentYear-01-01 00:00:00"
        );
    }
}
