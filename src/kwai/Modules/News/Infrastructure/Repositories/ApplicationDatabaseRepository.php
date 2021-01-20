<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Modules\News\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\News\Repositories\ApplicationQuery;
use Kwai\Modules\News\Repositories\ApplicationRepository;

/**
 * Class CategoryDatabaseRepository
 */
class ApplicationDatabaseRepository extends DatabaseRepository implements ApplicationRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery()->filterId($id);
        $entities = $this->getAll($query);
        if ($entities->count() === 0) {
            throw new ApplicationNotFoundException($id);
        }
        return $entities->first();
    }

    /**
     * Factory method for ApplicationQuery
     *
     * @return ApplicationQuery
     */
    public function createQuery(): ApplicationQuery
    {
        return new ApplicationDatabaseQuery($this->db);
    }
}
