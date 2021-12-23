<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Coaches\Domain\Exceptions\MemberNotFoundException;
use Kwai\Modules\Coaches\Domain\Member;

/**
 * Interface MemberRepository
 */
interface MemberRepository
{
    /**
     * Get the member with the given id
     *
     * @param int $id
     * @return Entity<Member>
     * @throws RepositoryException
     * @throws MemberNotFoundException
     */
    public function getById(int $id): Entity;

    /**
     * Creates a query.
     *
     * @return MemberQuery
     */
    public function createQuery(): MemberQuery;

    /**
     * Executes the query and returns a collection with entities.
     *
     * @param MemberQuery|null $query
     * @param int|null         $limit
     * @param int|null         $offset
     * @return Collection
     * @throws RepositoryException
     */
    public function getAll(
        ?MemberQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection;
}
