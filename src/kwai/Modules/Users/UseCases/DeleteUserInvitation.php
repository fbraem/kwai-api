<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;

/**
 * Class DeleteUserInvitation
 *
 * Deletes a user invitation. Confirmed invitations can't be deleted.
 */
class DeleteUserInvitation
{
    public function __construct(
        private UserInvitationRepository $repo
    ) {
    }

    public static function create(UserInvitationRepository $repo)
    {
        return new self($repo);
    }

    /**
     * @throws RepositoryException
     * @throws UserInvitationNotFoundException
     * @throws NotAllowedException
     */
    public function __invoke(DeleteUserInvitationCommand $command)
    {
        $invitation = $this->repo->getByUniqueId(new UniqueId($command->uuid));
        if ($invitation->isConfirmed()) {
            throw new NotAllowedException(
                'User invitation',
                'Delete',
                'A confirmed user invitation cannot be deleted'
            );
        }
        $this->repo->remove($invitation);
    }
}
