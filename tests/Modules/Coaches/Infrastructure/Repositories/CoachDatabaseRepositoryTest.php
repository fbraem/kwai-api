<?php
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Coaches\Infrastructure\Repositories\CoachDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can get a coach with a given id', function () {
    $repo = new CoachDatabaseRepository($this->db);
    try {
        $coach = $repo->getById(1);
        expect($coach)
            ->toBeInstanceOf(Entity::class)
            ->and($coach->getMember()->getName())
            ->toBeInstanceOf(Name::class)
            ->and($coach->getMember()->getName()->getFirstName())
            ->toBeString()
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
