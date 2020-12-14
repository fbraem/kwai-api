<?php

declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Trainings\Infrastructure\Mappers\CreatorMapper;

it('can map a record to a Creator', function () {
   $record = collect([
       'id' => '1',
       'first_name' => 'Jigoro',
       'last_name' => 'Kano'
   ]);

   $creator = CreatorMapper::toDomain($record);

   expect($creator)
       ->toBeInstanceOf(Creator::class)
       ->and($creator->getName())
       ->toBeInstanceOf(Name::class)
       ->and($creator->getName()->getFirstName())
       ->toBe('Jigoro')
   ;
});
