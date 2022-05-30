<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use DateTime;
use Exception;
use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Domain\UserInvitationEntity;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can create an invitation', function () {
    $repo = new UserInvitationDatabaseRepository($this->db);
    $userRepo = new UserDatabaseRepository($this->db);

    try {
        $query = $userRepo
            ->createQuery()
            ->filterByEmail(
                new EmailAddress('jigoro.kano@kwai.com')
            );
        $users = $userRepo->getAll($query, 1);
        expect($users->isEmpty())
            ->toBeFalse()
        ;
        $user = $users->first();

        $invitation = new UserInvitation(
            emailAddress: new EmailAddress('gella.vandecaveye@kwai.com'),
            expiration: new LocalTimestamp(
                Timestamp::createFromDateTime(new DateTime("now +14 days")),
                'Europe/Brussels'),
            name: 'Gella Vandecaveye',
            creator: new Creator(
                id: $user->id(),
                name: $user->getUsername()
            )
        );
        $invitation = $repo->create($invitation);
        expect($invitation)
            ->toBeInstanceOf(UserInvitationEntity::class)
        ;
        expect($invitation->domain())
            ->toBeInstanceOf(UserInvitation::class)
        ;
        return $invitation;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can get an invitation using an email', function () {
    $repo = new UserInvitationDatabaseRepository($this->db);
    try {
        $query = $repo
            ->createQuery()
            ->filterByEmail(
                new EmailAddress('gella.vandecaveye@kwai.com')
            )
        ;
        $invitations = $repo->getAll($query);
        expect($invitations)
            ->toBeInstanceOf(Collection::class)
            ->and($invitations->count())
            ->toBeGreaterThan(0)
            ->and($invitations->first())
            ->toBeInstanceOf(UserInvitationEntity::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->depends('it can create an invitation')
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can browse invitations', function () {
    $repo = new UserInvitationDatabaseRepository($this->db);
    $query = $repo
        ->createQuery()
        ->filterActive(Timestamp::createNow())
    ;
    try {
        $invitations = $repo->getAll($query);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    expect($invitations)
        ->toBeInstanceOf(Collection::class);
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
