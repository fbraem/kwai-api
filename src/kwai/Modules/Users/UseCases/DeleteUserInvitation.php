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
use Kwai\Modules\Users\Services\UserInvitationService;

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
        $service = new UserInvitationService($this->repo);
        $invitation = $service->getInvitation(new UniqueId($command->uuid));
        $service->remove($invitation);
    }
}
