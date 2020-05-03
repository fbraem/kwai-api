<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\News\Domain\ValueObjects;

use Carbon\Carbon;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\News\Domain\ValueObjects\Promotion;
use PHPUnit\Framework\TestCase;

class PromotionTest extends TestCase
{
    public function testIsEnabled()
    {
        $promotion = new Promotion();
        self::assertFalse($promotion->isEnabled(), 'Promotion should not be enabled by default');

        $promotion = new Promotion(1);
        self::assertTrue($promotion->isEnabled(), 'Promotion should be enabled');

        $promotion = new Promotion(1, Timestamp::createNow());
        self::assertTrue($promotion->isEnabled(), 'Promotion should be enabled');
    }

    public function testIsActive()
    {
        $promotion = new Promotion();
        self::assertFalse($promotion->isActive(), 'Promotion should not be active by default');

        $promotion = new Promotion(1);
        self::assertTrue($promotion->isActive(), 'Promotion should be active');

        $timeStamp = Carbon::now();
        $timeStamp->subDays(7);
        $promotion = new Promotion(1, Timestamp::createFromString($timeStamp->toDateTimeString()));
        self::assertFalse($promotion->isActive(), 'Promotion should not be active');

        $timeStamp = Carbon::now();
        $timeStamp->addDays(7);
        $promotion = new Promotion(1, Timestamp::createFromString($timeStamp->toDateTimeString()));
        self::assertTrue($promotion->isActive(), 'Promotion should be active');
    }
}
