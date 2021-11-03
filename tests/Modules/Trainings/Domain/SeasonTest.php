<?php

declare(strict_types=1);

use Kwai\Modules\Trainings\Domain\Season;

it('can construct a season', function () {
   $season = new Season(name: '2020-2021');
   expect($season->getName())
       ->toBe('2020-2021')
   ;
});
