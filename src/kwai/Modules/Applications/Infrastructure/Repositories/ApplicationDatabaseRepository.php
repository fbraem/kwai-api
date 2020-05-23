<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Applications\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Applications\Repositories\ApplicationQuery;
use Kwai\Modules\Applications\Repositories\ApplicationRepository;

/**
 * Class ApplicationDatabaseRepository
 */
class ApplicationDatabaseRepository extends DatabaseRepository implements ApplicationRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery();
        $query->filterId($id);

        try {
            $entities = $query->execute();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if (count($entities) == 1) {
            return $entities[$id];
        }
        throw new ApplicationNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): ApplicationQuery
    {
        return new ApplicationDatabaseQuery($this->db);
    }
}
