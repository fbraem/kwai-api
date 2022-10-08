<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\UserRecovery;
use Kwai\Modules\Users\Infrastructure\Repositories\UserRecoveryDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can recover a user', function () {
    $repo = new UserRecoveryDatabaseRepository($this->db);
    $recovery = new UserRecovery();
    $repo->create($recovery);
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can get all recovers', function () {
    $repo = new UserRecoveryDatabaseRepository($this->db);
    try {
        $recoveries = $repo->getAll();
        expect($recoveries)
            ->toBeInstanceOf(Collection::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can retrieve a recovery', function () {

})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
