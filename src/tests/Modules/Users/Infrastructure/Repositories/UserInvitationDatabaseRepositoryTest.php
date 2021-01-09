<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use DateTime;
use Exception;
use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can create an invitation', function () use ($context) {
    $repo = new UserInvitationDatabaseRepository($context->db);
    $userRepo = new UserDatabaseRepository($context->db);

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
            emailAddress: new EmailAddress('invite@kwai.com'),
            expiration: Timestamp::createFromDateTime(new DateTime("now +14 days")),
            name: 'Jigoro Kano',
            creator: new Creator(
                id: $user->id(),
                name: $user->getUsername()
            )
        );
        $invitation = $repo->create($invitation);
        expect($invitation)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($invitation->domain())
            ->toBeInstanceOf(UserInvitation::class)
        ;
        return $invitation;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get an invitation using an email', function () use ($context) {
    $repo = new UserInvitationDatabaseRepository($context->db);
    try {
        $query = $repo
            ->createQuery()
            ->filterByEmail(
                new EmailAddress('invite@kwai.com')
            )
        ;
        $invitations = $repo->getAll($query);
        expect($invitations)
            ->toBeInstanceOf(Collection::class)
            ->and($invitations->count())
            ->toBeGreaterThan(0)
            ->and($invitations->first())
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    // ->depends('it can create an invitation')
    ->skip(!Context::hasDatabase(), 'No database available')
;
