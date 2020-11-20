<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\Exceptions\CoachNotFoundException;

/**
 * Interface CoachRepository
 */
interface CoachRepository
{
    /**
     * Get the coach with the given id.
     *
     * @throws RepositoryException
     * @throws CoachNotFoundException
     * @param int $id
     * @return Entity<Coach>
     */
    public function getById(int $id): Entity;
}
