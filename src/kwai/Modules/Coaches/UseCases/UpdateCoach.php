<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Coaches\Domain\Coach;
use Kwai\Modules\Coaches\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Coaches\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Coaches\Repositories\CoachRepository;
use Kwai\Modules\Coaches\Repositories\UserRepository;

/**
 * Class UpdateCoach
 *
 * Use case for updating a coach.
 */
class UpdateCoach
{
    public function __construct(
        private CoachRepository $coachRepo,
        private UserRepository $userRepo
    ) {
    }

    public static function create(
        CoachRepository $coachRepo,
        UserRepository $userRepo
    ): self {
        return new self($coachRepo, $userRepo);
    }

    /**
     * @throws RepositoryException
     * @throws CoachNotFoundException
     * @throws UserNotFoundException
     * @return Entity<Coach>
     */
    public function __invoke(UpdateCoachCommand $command): Entity
    {
        $coach = $this->coachRepo->getById($command->id);

        $user = isset($command->user_id) ? $this->userRepo->getById($command->user_id) : null;

        $traceableTime = $coach->getTraceableTime();
        $traceableTime->markUpdated();

        $coach = new Entity(
            $coach->id(),
            new Coach(
                member: $coach->getMember(),
                bio: $command->bio,
                diploma: $command->diploma,
                active: $command->active,
                user: $user ? $user : $coach->getUser(),
                remark: $command->remark,
                traceableTime: $traceableTime
            )
        );
        $this->coachRepo->update($coach);

        return $coach;
    }
}
