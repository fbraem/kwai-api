<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
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
     * @throws UserInvitationNotFoundException
     * @throws RepositoryException
     * @return Entity<UserInvitation> An invitation
     */
    public function getByUniqueId(UniqueId $uuid) : Entity;

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

    /**
     * Factory method
     *
     * @return UserInvitationQuery
     */
    public function createQuery(): UserInvitationQuery;

    /**
     * Get all user invitations using the given query. If no query is passed,
     * all user invitations will be returned.
     *
     * @param UserInvitationQuery|null $query
     * @param int|null                 $limit
     * @param int|null                 $offset
     * @throws RepositoryException
     * @return Collection
     */
    public function getAll(
        ?UserInvitationQuery $query,
        ?int $limit = null,
        ?int $offset = null
    ): Collection;
}
