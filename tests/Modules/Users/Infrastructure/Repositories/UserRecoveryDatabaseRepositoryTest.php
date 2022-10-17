<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserEntity;
use Kwai\Modules\Users\Domain\UserRecovery;
use Kwai\Modules\Users\Domain\UserRecoveryEntity;
use Kwai\Modules\Users\Infrastructure\Repositories\UserRecoveryDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can recover a user', function () {
    $repo = new UserRecoveryDatabaseRepository($this->db);
    $recovery = new UserRecovery(
        new UniqueId(),
        new LocalTimestamp(Timestamp::createNow(), 'UTC'),
        new UserEntity(
            1,
            new User(
                new UniqueId(),
                new EmailAddress('jigoro.kano@kwai.com'),
                new Name('Jigoro', 'Kano')
            )
        )
    );
    try {
        $recovery = $repo->create($recovery);
        expect($recovery)->toBeInstanceOf(UserRecoveryEntity::class);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
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
