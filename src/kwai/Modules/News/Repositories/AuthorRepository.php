<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Author;
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
     * @return Entity<Author>
     * @throws AuthorNotFoundException
     * @throws RepositoryException
     */
    public function getById(int $id): Entity;

    /**
     * Get the author with the given unique id
     *
     * @param UniqueId $uuid
     * @return Entity<Author>
     * @throws AuthorNotFoundException
     * @throws RepositoryException
     */
    public function getByUniqueId(UniqueId $uuid): Entity;
}
