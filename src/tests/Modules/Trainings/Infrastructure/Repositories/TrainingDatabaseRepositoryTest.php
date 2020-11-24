<?php
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Event;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get a training', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    try {
        $training = $repo->getById(1);
        expect($training)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($training->domain())
            ->toBeInstanceOf(Training::class)
        ;
        /** @noinspection PhpUndefinedMethodInspection */
        expect($training->getEvent())
            ->toBeInstanceOf(Event::class)
        ;
    } catch (TrainingNotFoundException $e) {
        $this->fail((string) $e);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can count all trainings', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    try {
        $query = $repo->createQuery();
        $count = $query->count();
        expect($count)
            ->toBeGreaterThan(0)
        ;
    } catch (QueryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;