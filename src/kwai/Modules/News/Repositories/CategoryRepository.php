<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\QueryException;
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
     * @throws QueryException
     */
    public function getById(int $id): Entity;
}
