<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\QueryException;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;

/**
 * Interface AuthorRepository
 */
interface AuthorRepository
{
    /**
     * Get the author with the given id
     *
     * @param int $id
     * @return Entity
     * @throws AuthorNotFoundException
     * @throws QueryException
     */
    public function getById(int $id): Entity;
}
