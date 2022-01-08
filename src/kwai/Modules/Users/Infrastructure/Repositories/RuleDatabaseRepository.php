<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleDTO;
use Kwai\Modules\Users\Repositories\RuleQuery;
use Kwai\Modules\Users\Repositories\RuleRepository;

/**
 * RuleDatabaseRepository
 */
class RuleDatabaseRepository extends DatabaseRepository implements RuleRepository
{
    /**
     * RuleDatabaseRepository constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            static fn(RuleDTO $dto) => $dto->createEntity()
        );
    }

    /**
     * @inheritDoc
     */
    public function getByIds(array $ids): Collection
    {
        $query = $this->createQuery();
        $query->filterById(...$ids);
        return $this->getAll($query);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): RuleQuery
    {
        return new RuleDatabaseQuery($this->db);
    }
}
