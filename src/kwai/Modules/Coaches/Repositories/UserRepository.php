<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Coaches\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Coaches\Domain\User;

/**
 * Interface UserRepository
 */
interface UserRepository
{
    /**
     * Get the user with the given id
     *
     * @param int $id
     * @return Entity<User>
     * @throws RepositoryException
     * @throws UserNotFoundException
     */
    public function getById(int $id): Entity;
}
