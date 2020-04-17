<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class AttachAbilityToUser
 *
 * Use case to attach an ability to a user.
 * - Step 1 - Get the user
 * - Step 2 - Get the abilities of the user
 * - Step 3 - Get the ability
 * - Step 4 - Add the ability
 */
class AttachAbilityToUser
{
    private UserRepository $userRepo;

    private AbilityRepository $abilityRepo;

    /**
     * AttachAbilityToUser constructor.
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
     * @param AttachAbilityToUserCommand $command
     * @return Entity
     * @throws NotFoundException
     * @throws RepositoryException
     */
    public function __invoke(AttachAbilityToUserCommand $command): Entity
    {
        $user = $this->userRepo->getByUUID(new UniqueId($command->uuid));
        $abilities = $this->abilityRepo->getByUser($user);
        /** @noinspection PhpUndefinedMethodInspection */
        $user->setAbilities($abilities);

        // Already added
        if (isset($abilities[$command->abilityId])) {
            return $user;
        }

        // Add it
        $ability = $this->abilityRepo->getById($command->abilityId);
        $this->userRepo->addAbility($user, $ability);

        return $user;
    }
}
