<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
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
    private UserRepository $userRepo;

    private AbilityRepository $abilityRepo;

    /**
     * DetachAbilityFromUser constructor.
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
     * @param DetachAbilityFromUserCommand $command
     * @return Entity
     * @throws NotFoundException
     * @throws RepositoryException
     */
    public function __invoke(DetachAbilityFromUserCommand $command): Entity
    {
        $user = $this->userRepo->getByUUID(new UniqueId($command->uuid));
        $abilities = $this->abilityRepo->getByUser($user);
        /** @noinspection PhpUndefinedMethodInspection */
        $user->setAbilities($abilities);

        // Not attached, so return the user
        if (!isset($abilities[$command->abilityId])) {
            return $user;
        }

        // Remove it
        $this->userRepo->removeAbility($user, $abilities[$command->abilityId]);

        return $user;
    }
}
