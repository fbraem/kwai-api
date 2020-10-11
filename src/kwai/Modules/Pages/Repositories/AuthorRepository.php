<?php
/**
 * @package Pages
 * @subpackage Repositories
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Author;
use Kwai\Modules\Pages\Domain\Exceptions\AuthorNotFoundException;

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
     * @return Entity
     * @throws AuthorNotFoundException
     * @throws RepositoryException
     */
    public function getByUniqueId(UniqueId $uuid): Entity;
}
