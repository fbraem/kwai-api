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
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class GetUser
 *
 * Use case to get a user with the given unique id.
 * Abilities are also returned when withAbilities is set to true.
 */
class GetUser
{
    private UserRepository $userRepo;

    private AbilityRepository $abilityRepo;

    public function __construct(UserRepository $userRepo, AbilityRepository $abilityRepo)
    {
        $this->userRepo = $userRepo;
        $this->abilityRepo = $abilityRepo;
    }

    /**
     * Get a user
     *
     * @param GetUserCommand $command
     * @return Entity<User>
     * @throws NotFoundException
     * @throws RepositoryException
     */
    public function __invoke(GetUserCommand $command): Entity
    {
        $user = $this->userRepo->getByUUID(new UniqueId($command->uuid));
        if ($command->withAbilities) {
            $abilities = $this->abilityRepo->getByUser($user);
            foreach ($abilities as $ability) {
                $user->domain()->addAbility($ability);
            }
        }
        return $user;
    }
}
