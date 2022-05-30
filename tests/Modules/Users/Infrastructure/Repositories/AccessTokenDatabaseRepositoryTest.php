<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use DateTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\AccessTokenEntity;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\UserAccountEntity;
use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can create an accesstoken', function () {
    $accessToken = new AccessToken(
        identifier: new TokenIdentifier(),
        expiration: Timestamp::createFromDateTime(
            new DateTime('now +2 hours')
        ),
        account: new UserAccountEntity(
            $this->withUser()->id(),
            new UserAccount(
                user: $this->withUser()->domain(),
                password: Password::fromString('test')
            )
        )
    );
    $repo = new AccessTokenDatabaseRepository($this->db);
    try {
        $entity = $repo->create($accessToken);
        expect($entity)
            ->toBeInstanceOf(AccessTokenEntity::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
