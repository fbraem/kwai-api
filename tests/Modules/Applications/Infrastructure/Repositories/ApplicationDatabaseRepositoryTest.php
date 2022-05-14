<?php
declare(strict_types=1);

namespace Tests\Modules\Applications\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Applications\Domain\Application;
use Kwai\Modules\Applications\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Applications\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);

beforeEach(fn() => $this->withDatabase());

it('can retrieve an application', function () {
    $repo = new ApplicationDatabaseRepository($this->db);
    try {
        $application = $repo->getById(1);
        expect($application)->toBeInstanceOf(Entity::class);
        expect($application->domain())->toBeInstanceOf(Application::class);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, strval($e));
    } catch (ApplicationNotFoundException $e) {
        $this->assertTrue(false, strval($e));
    }
})
    ->skip(fn() => !$this->hasDatabase())
;
