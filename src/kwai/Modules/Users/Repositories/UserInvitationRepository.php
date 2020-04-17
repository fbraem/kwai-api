<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\UserInvitation;

/**
 * UserInvitation repository interface
 */
interface UserInvitationRepository
{
    /**
     * Get an invitation by its unique id.
     *
     * @param  UniqueId $uuid A unique id
     * @throws NotFoundException
     * @throws RepositoryException
     * @return Entity<UserInvitation> An invitation
     */
    public function getByUniqueId(UniqueId $uuid) : Entity;

    /**
     * Get all active invitations
     * @throws RepositoryException
     * @return Entity<UserInvitation>[]
     */
    public function getActive(): array;

    /**
     * Get all invitations for the given email address.
     * @param EmailAddress $email
     * @throws RepositoryException
     * @return Entity<UserInvitation>[]
     */
    public function getByEmail(EmailAddress $email): array;

    /**
     * Save a new UserInvitation
     * @param  UserInvitation $invitation
     * @throws RepositoryException
     * @return Entity<UserInvitation>
     */
    public function create(UserInvitation $invitation): Entity;

    /**
     * Update the invitation.
     * @throws RepositoryException
     * @param  Entity<UserInvitation> $invitation
     */
    public function update(Entity $invitation): void;
}
