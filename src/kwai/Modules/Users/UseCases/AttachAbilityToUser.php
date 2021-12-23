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
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class AttachAbilityToUser
 *
 * Use case to attach an ability to a user.
 */
class AttachAbilityToUser
{
    /**
     * AttachAbilityToUser constructor.
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
     * @param UserDatabaseRepository    $userRepo
     * @param AbilityDatabaseRepository $abilityRepo
     * @return static
     */
    public static function create(
        UserDatabaseRepository $userRepo,
        AbilityDatabaseRepository $abilityRepo
    ): self {
        return new self($userRepo, $abilityRepo);
    }

    /**
     * @param AttachAbilityToUserCommand $command
     * @return Entity
     * @throws RepositoryException
     * @throws UserNotFoundException
     * @throws AbilityNotFoundException
     */
    public function __invoke(AttachAbilityToUserCommand $command): Entity
    {
        $user = $this->userRepo->getByUniqueId(new UniqueId($command->uuid));
        $ability = $this->abilityRepo->getById($command->abilityId);
        /** @noinspection PhpUndefinedMethodInspection */
        $user->addAbility($ability);
        $this->userRepo->update($user);

        return $user;
    }
}
