<?php

declare(strict_types=1);

use Kwai\Modules\Trainings\Domain\Team;

it('can construct a team', function () {
   $team = new Team(name: 'Competitors');
   expect($team->getName())
       ->toBe('Competitors')
   ;
});
