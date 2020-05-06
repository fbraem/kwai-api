<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\CategoryNotFoundException;

/**
 * Interface CategoryRepository
 */
interface CategoryRepository
{
    /**
     * @param int $id
     * @return Entity
     * @throws CategoryNotFoundException
     * @throws RepositoryException
     */
    public function getById(int $id): Entity;
}
