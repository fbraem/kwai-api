<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Timestamp;

use Kwai\Modules\Users\Repositories\UserRepository;
use Kwai\Modules\Users\Repositories\AbilityRepository;

use Kwai\Modules\Users\Domain\User;

/**
 * Usecase: Get current user with his/her abilities
 */
final class GetCurrentUser
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var AbilityRepository
     */
    private $abilityRepo;

    /**
     * Constructor.
     * @param UserRepository $userRepo A user repository
     * @param AbilityRepository $abilityRepo An ability repository
     */
    public function __construct(
        UserRepository $userRepo,
        AbilityRepository $abilityRepo
    ) {
        $this->userRepo = $userRepo;
        $this->abilityRepo = $abilityRepo;
    }

    /**
     * Get the user with the given uuid.
     * @param  GetCurrentUserCommand $command
     * @return Entity<User>                   A user with his/her abilitities
     * @throws \Kwai\Core\Domain\Exceptions\NotFoundException
     */
    public function __invoke(GetCurrentUserCommand $command): Entity
    {
        $user = $this->userRepo->getByUUID(new UniqueId($command->uuid));
        $abilities = $this->abilityRepo->getByUser($user);
        foreach ($abilities as $ability) {
            $user->addAbility($ability);
        }
        return $user;
    }
}
