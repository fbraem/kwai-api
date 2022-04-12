<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Domain\UserInvitationEntity;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;

/**
 * Class GetUserInvitation
 *
 * Use case to get a user invitation with the given unique id.
 */
class GetUserInvitation
{
    /**
     * GetUserInvitation constructor.
     *
     * @param UserInvitationRepository $repo
     */
    public function __construct(
        private readonly UserInvitationRepository $repo
    ) {
    }

    /**
     * Factory method
     *
     * @param UserInvitationRepository $repo
     * @return static
     */
    public static function create(UserInvitationRepository $repo): self
    {
        return new self($repo);
    }

    /**
     * Get a user invitation
     *
     * @param GetUserInvitationCommand $command
     * @return UserInvitationEntity
     * @throws RepositoryException
     * @throws UserInvitationNotFoundException
     */
    public function execute(GetUserInvitationCommand $command): UserInvitationEntity
    {
        return $this->repo->getByUniqueId(new UniqueId($command->uuid));
    }
}
