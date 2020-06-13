<?php
declare(strict_types=1);

namespace Tests\Core\Domain;

use Kwai\Core\Domain\ValueObjects\Date;

it(
    'can create a validate date',
    function () {
        $date = Date::createFromDate();
        assertInstanceOf(Date::class, $date);
    }
);
