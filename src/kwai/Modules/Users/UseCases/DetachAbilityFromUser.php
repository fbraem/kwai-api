<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\AbilityNotFoundException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class DetachAbilityFromUser
 *
 * Use case to detach an ability from a user.
 * - Step 1 - Get the user
 * - Step 2 - Get the abilities of the user
 * - Step 4 - Remove the ability if present
 */
class DetachAbilityFromUser
{
    /**
     * DetachAbilityFromUser constructor.
     *
     * @param UserRepository    $userRepo
     * @param AbilityRepository $abilityRepo
     */
    public function __construct(
        private UserRepository $userRepo,
        private AbilityRepository $abilityRepo
    ) {
        $this->userRepo = $userRepo;
        $this->abilityRepo = $abilityRepo;
    }

    /**
     * Factory method
     *
     * @param UserRepository    $userRepo
     * @param AbilityRepository $abilityRepository
     * @return DetachAbilityFromUser
     */
    public static function create(UserRepository $userRepo, AbilityRepository $abilityRepository): self
    {
        return new self($userRepo, $abilityRepository);
    }

    /**
     * @param DetachAbilityFromUserCommand $command
     * @return Entity
     * @throws RepositoryException
     * @throws UserNotFoundException
     * @throws AbilityNotFoundException
     */
    public function __invoke(DetachAbilityFromUserCommand $command): Entity
    {
        $user = $this->userRepo->getByUniqueId(new UniqueId($command->uuid));
        $ability = $this->abilityRepo->getById($command->abilityId);

        /** @noinspection PhpUndefinedMethodInspection */
        $user->removeAbility($ability);

        $this->userRepo->update($user);

        return $user;
    }
}
