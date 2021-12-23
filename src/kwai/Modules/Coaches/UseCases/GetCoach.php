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
use Kwai\Modules\Coaches\Repositories\CoachRepository;

/**
 * Class GetCoach
 *
 * Use case for getting a coach.
 */
class GetCoach
{
    public function __construct(
        private CoachRepository $repo
    ) {
    }

    public static function create(CoachRepository $repo): self
    {
        return new self($repo);
    }

    /**
     * @return Entity<Coach>
     * @throws RepositoryException
     * @throws CoachNotFoundException
     */
    public function __invoke(GetCoachCommand $command): Entity
    {
        return $this->repo->getById($command->id);
    }
}
