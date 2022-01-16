<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class UpdateUser
 *
 * Use case for updating a user.
 */
class UpdateUser
{
    /**
     * Constructor.
     *
     * @param UserRepository    $userRepo
     * @param AbilityRepository $abilityRepository
     */
    public function __construct(
        private UserRepository $userRepo,
        private AbilityRepository $abilityRepository
    ) {
    }

    /**
     * Factory method.
     *
     * @param UserRepository    $userRepo
     * @param AbilityRepository $abilityRepository
     * @return UpdateUser
     */
    public static function create(
        UserRepository $userRepo,
        AbilityRepository $abilityRepository
    ) : self
    {
        return new self($userRepo, $abilityRepository);
    }

    /**
     * Executes the use case.
     *
     * @throws UserNotFoundException
     * @throws RepositoryException
     * @throws QueryException
     * @throws UnprocessableException
     */
    public function __invoke(UpdateUserCommand $command, Entity $activeUser): Entity
    {
        $user = $this->userRepo->getByUniqueId(new UniqueId($command->uuid));

        if ($command->email && (string) $user->getEmailAddress() != $command->email) {
            $email = new EmailAddress($command->email);
            $userQuery = $this->userRepo->createQuery();
            $userQuery->filterByEmail($email);
            if ( $userQuery->count() > 0 ) {
                throw new UnprocessableException(
                    $email . ' is already in use.'
                );
            }
        }

        $traceableTime = $user->getTraceableTime();
        $traceableTime->markUpdated();

        $user = new Entity(
            $user->id(),
            new User(
                uuid: $user->getUuid(),
                emailAddress: $email ?? $user->getEmailAddress(),
                username: new Name($command->first_name, $command->last_name),
                abilities: $user->getAbilities(),
                remark: $command->remark,
                member: $user->getMember(),
                traceableTime: $traceableTime
            )
        );

        $this->userRepo->update($user);

        if ($command->abilities && count($command->abilities) > 0) {
            $query = $this->abilityRepository->createQuery();
            $query->filterByIds(...$command->abilities);
            $abilities = $this->abilityRepository->getAll($query);

            $abilitiesToAdd = $abilities->diffKeys($user->getAbilities());
            $abilitiesToAdd->each(fn (Entity $ability) => $user->addAbility($ability));
            $this->userRepo->insertAbilities($user, $abilitiesToAdd);

            $abilitiesToRemove = $user->getAbilities()->diffKeys($abilities);
            $abilitiesToRemove->each(fn (Entity $ability) => $user->removeAbility($ability));
            $this->userRepo->deleteAbilities($user, $abilitiesToRemove);
        }

        return $user;
    }
}
