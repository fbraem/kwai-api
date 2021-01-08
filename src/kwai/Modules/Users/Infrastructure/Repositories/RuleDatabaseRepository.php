<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleMapper;
use Kwai\Modules\Users\Repositories\RuleQuery;
use Kwai\Modules\Users\Repositories\RuleRepository;

/**
 * RuleDatabaseRepository
 */
class RuleDatabaseRepository extends DatabaseRepository implements RuleRepository
{
    /**
     * @inheritDoc
     */
    public function getAll(
        ?RuleQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection
    {
        $query ??= $this->createQuery();

        /* @var Collection $rules */
        $rules = $query->execute($limit, $offset);
        return $rules->mapWithKeys(
            fn($item, $key) => [
                $key => new Entity((int) $key, RuleMapper::toDomain($item))
            ]
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
