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

it('can check if a promotion is enabled', function () {
    $promotion = new Promotion();
    expect($promotion->isEnabled())
        ->toBe(false)
    ;

    $promotion = new Promotion(1);
    expect($promotion->isEnabled())
        ->toBe(true)
    ;

    $promotion = new Promotion(1, Timestamp::createNow());
    expect($promotion->isEnabled())
        ->toBe(true)
    ;
});

it('can check if a promotion is active', function () {
    $promotion = new Promotion();
    expect($promotion->isActive())
        ->toBe(false)
    ;

    $promotion = new Promotion(1);
    expect($promotion->isActive())
        ->toBe(true)
    ;

    $timeStamp = Carbon::now();
    $timeStamp->subDays(7);
    $promotion = new Promotion(
        1,
        Timestamp::createFromString($timeStamp->toDateTimeString())
    );
    expect($promotion->isActive())
        ->toBe(false)
    ;

    $timeStamp = Carbon::now();
    $timeStamp->addDays(7);
    $promotion = new Promotion(
        1,
        Timestamp::createFromString($timeStamp->toDateTimeString())
    );
    expect($promotion->isActive())
        ->toBe(true)
    ;
});
