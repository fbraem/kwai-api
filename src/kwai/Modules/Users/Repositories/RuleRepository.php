<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);


namespace Kwai\Modules\Users\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\QueryException;

interface RuleRepository
{
    /**
     * Get all rules with the given ids. The id is used
     * as key of the returned array.
     *
     * @param int[] $ids
     * @return Collection
     * @throws QueryException
     */
    public function getByIds(array $ids): Collection;

    /**
     * Factory method to create the related query.
     *
     * @return RuleQuery
     */
    public function createQuery(): RuleQuery;

    /**
     * Get all rules with the given query. If no query is passed, all rules
     * will be returned.
     *
     * @param RuleQuery|null $query
     * @param int|null       $limit
     * @param int|null       $offset
     * @return Collection
     * @throws QueryException
     */
    public function getAll(
        ?RuleQuery $query = null,
        ?int $limit = null,
        ?int $offset = 0
    ): Collection;
}
