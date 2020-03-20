<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\UniqueId;
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
     * @return Entity<UserInvitation> An invitation
     * @throws NotFoundException
     */
    public function getByUniqueId(UniqueId $uuid) : Entity;

    /**
     * Get all active invitations
     * @return Entity<UserInvitation>[]
     */
    public function getActive(): array;

    /**
     * Get all invitations for the given email address.
     * @param EmailAddress $email
     * @return Entity<UserInvitation>[]
     */
    public function getByEmail(EmailAddress $email): array;

    /**
     * Save a new UserInvitation
     * @param  UserInvitation $invitation
     * @return Entity<UserInvitation>
     */
    public function create(UserInvitation $invitation): Entity;

    /**
     * Update the invitation.
     * @param  Entity<UserInvitation> $invitation
     */
    public function update(Entity $invitation): void;
}
