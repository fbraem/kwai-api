<?php

declare(strict_types=1);

namespace Tests\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Application;
use Kwai\Modules\News\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);

beforeEach(fn() => $this->withDatabase());

it('can retrieve an application by id', function ()  {
    $repo = new ApplicationDatabaseRepository($this->db);
    try {
        $application = $repo->getById(1);
        expect($application)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($application->domain())
            ->toBeInstanceOf(Application::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    } catch (ApplicationNotFoundException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
