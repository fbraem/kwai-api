<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class GetUserAbilities
 *
 * Use case that returns all abilities of a user with the given uuid
 */
class GetUserAbilities
{
    private UserRepository $userRepo;

    private AbilityRepository $abilityRepo;

    /**
     * GetUserAbilities constructor.
     *
     * @param UserRepository    $userRepo
     * @param AbilityRepository $abilityRepo
     */
    public function __construct(UserRepository $userRepo, AbilityRepository $abilityRepo)
    {
        $this->userRepo = $userRepo;
        $this->abilityRepo = $abilityRepo;
    }

    /**
     * Get a user
     *
     * @param GetUserAbilitiesCommand $command
     * @return Entity<Ability>[]
     * @throws NotFoundException
     * @throws RepositoryException
     */
    public function __invoke(GetUserAbilitiesCommand $command): array
    {
        $user = $this->userRepo->getByUUID(new UniqueId($command->uuid));
        return $this->abilityRepo->getByUser($user);
    }
}
