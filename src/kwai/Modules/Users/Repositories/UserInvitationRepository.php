<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Domain\UserInvitationEntity;

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
     * @return UserInvitationEntity An invitation
     */
    public function getByUniqueId(UniqueId $uuid) : UserInvitationEntity;

    /**
     * Save a new UserInvitation
     * @param  UserInvitation $invitation
     * @throws RepositoryException
     * @return UserInvitationEntity
     */
    public function create(UserInvitation $invitation): UserInvitationEntity;

    /**
     * Update the invitation.
     * @throws RepositoryException
     * @param  UserInvitationEntity $invitation
     */
    public function update(UserInvitationEntity $invitation): void;

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
        ?UserInvitationQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection;

    /**
     * Removes an invitation.
     *
     * @param UserInvitationEntity $invitation
     * @throws RepositoryException
     */
    public function remove(UserInvitationEntity $invitation): void;
}
