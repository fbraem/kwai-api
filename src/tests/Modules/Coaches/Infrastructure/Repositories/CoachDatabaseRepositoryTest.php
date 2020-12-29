<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Coaches\Infrastructure\Repositories\CoachDatabaseRepository;
use Tests\Context;

$context = Context::createContext();
it('can get a coach with a given id', function () use ($context) {
    $repo = new CoachDatabaseRepository($context->db);
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
    ->skip(!Context::hasDatabase(), 'No database available')
;
