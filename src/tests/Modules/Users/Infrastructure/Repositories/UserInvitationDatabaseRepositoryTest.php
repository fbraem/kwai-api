<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use DateTime;
use Exception;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\DatabaseException;
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
        $user = $userRepo->getByEmail(new EmailAddress('test@kwai.com'));
        $invitation = new UserInvitation((object) [
            'uuid' => new UniqueId(),
            'emailAddress' => new EmailAddress('invite@kwai.com'),
            'traceableTime' => new TraceableTime(),
            'expiration' => Timestamp::createFromDateTime(new DateTime("now +14 days")),
            'name' => 'Jigoro Kano',
            'creator' => $user
        ]);
        $invitation = $repo->create($invitation);
        expect($invitation)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($invitation->domain())
            ->toBeInstanceOf(UserInvitation::class)
        ;
        $this->assertInstanceOf(Entity::class, $invitation);
        return $invitation;
    } catch (DatabaseException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (NotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (Exception $e) {
        $this->assertTrue(false, (string) $e);
    }
    return null;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get an invitation using an email', function () use ($context) {
    $repo = new UserInvitationDatabaseRepository($context->db);
    try {
        $invitations = $repo->getByEmail(new EmailAddress('invite@kwai.com'));
        expect($invitations)
            ->toBeArray()
        ;
        $this->assertContainsOnlyInstancesOf(
            Entity::class,
            $invitations
        );
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->depends('it can create an invitation')
    ->skip(!Context::hasDatabase(), 'No database available')
;
