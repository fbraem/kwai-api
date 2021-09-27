<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Coaches\Domain\Coach;
use Kwai\Modules\Coaches\Domain\Exceptions\CoachAlreadyExistsException;
use Kwai\Modules\Coaches\Domain\Exceptions\MemberNotFoundException;
use Kwai\Modules\Coaches\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Coaches\Infrastructure\Repositories\CoachDatabaseRepository;
use Kwai\Modules\Coaches\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Coaches\Repositories\MemberRepository;

/**
 * Class CreateCoach
 *
 * Use case for creating a coach.
 */
class CreateCoach
{
    /**
     * Constructor.
     *
     * @param CoachDatabaseRepository $coachRepo
     * @param UserDatabaseRepository  $userRepo
     */
    public function __construct(
        private CoachDatabaseRepository $coachRepo,
        private MemberRepository $memberRepo,
        private UserDatabaseRepository $userRepo
    ) {
    }

    /**
     * Factory method.
     *
     * @param CoachDatabaseRepository $coachRepo
     * @param UserDatabaseRepository  $userRepo
     * @return self
     */
    public static function create(
        CoachDatabaseRepository $coachRepo,
        MemberRepository $memberRepo,
        UserDatabaseRepository $userRepo
    ): self {
        return new self($coachRepo, $memberRepo, $userRepo);
    }

    /**
     * @throws RepositoryException
     * @throws UserNotFoundException
     * @throws MemberNotFoundException
     * @throws CoachAlreadyExistsException
     * @return Entity<Coach>
     */
    public function __invoke(CreateCoachCommand $command): Entity
    {
        $user = isset($command->user_id) ? $this->userRepo->getById($command->user_id) : null;

        $member = $this->memberRepo->getById($command->member_id);

        $coaches = $this->coachRepo->getAll(
            $this->coachRepo->createQuery()
                ->filterMember($command->member_id)
        );
        if (count($coaches) > 0) {
            throw new CoachAlreadyExistsException($command->member_id);
        }

        return $this->coachRepo->create(
            new Coach(
                member: $member,
                active: $command->active,
                bio: $command->bio,
                diploma: $command->diploma,
                user: $user
            )
        );
    }
}
