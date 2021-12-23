<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;

/**
 * Class BrowseUserInvitations
 *
 * Use case to browse all users invitations.
 */
class BrowseUserInvitations
{
    public function __construct(private UserInvitationRepository $repo)
    {
    }

    /**
     * Factory method
     *
     * @param UserInvitationRepository $repo
     * @return BrowseUserInvitations
     */
    public static function create(UserInvitationRepository $repo)
    {
        return new self($repo);
    }

    /**
     * Browse all user invitations and returns a list
     *
     * @param BrowseUserInvitationsCommand $command
     * @return array
     * @throws QueryException
     * @throws RepositoryException
     */
    public function __invoke(BrowseUserInvitationsCommand $command): array
    {
        $query = $this->repo->createQuery();

        if ($command->active_time && $command->active_timezone) {
            $query->filterActive(Timestamp::createFromString($command->active_time, $command->active_timezone));
        }

        return [
            $query->count(),
            $this->repo->getAll($query, $command->limit, $command->offset)
        ];
    }
}
