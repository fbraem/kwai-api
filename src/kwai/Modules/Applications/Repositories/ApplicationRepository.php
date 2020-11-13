<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Applications\Domain\Application;
use Kwai\Modules\Applications\Domain\Exceptions\ApplicationNotFoundException;

/**
 * Interface ApplicationRepository
 */
interface ApplicationRepository
{
    /**
     * Get the application by id
     *
     * @param int $id
     * @return Entity<Application>
     * @throws RepositoryException
     * @throws ApplicationNotFoundException
     */
    public function getById(int $id): Entity;

    /**
     * Create a query
     *
     * @return ApplicationQuery
     */
    public function createQuery(): ApplicationQuery;
}
