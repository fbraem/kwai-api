<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class BrowseUser
 *
 * Use case to browse all users.
 */
class BrowseUser
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Browse all users and returns a list
     *
     * @param BrowseUserCommand $command
     * @return Entity[]
     */
    public function __invoke(BrowseUserCommand $command): array
    {
        return $this->userRepo->getAll();
    }
}
