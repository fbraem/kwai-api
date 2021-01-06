<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\AbilityNotFoundException;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Infrastructure\Mappers\AbilityMapper;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\AbilityQuery;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class AbilityDatabaseRepository
 *
 * Ability repository for read/write Ability entity from/to a database.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
final class AbilityDatabaseRepository extends DatabaseRepository implements AbilityRepository
{
    /**
     * @inheritDoc
     * @return Entity<Ability>
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery()->filterById($id);

        try {
            $entities = $this->getAll($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($entities->isNotEmpty()) {
            return $entities->get($id);
        }

        throw new AbilityNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function create(Ability $ability): Entity
    {
        $data = AbilityMapper::toPersistence($ability);

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::ABILITIES())
            ->columns(
                ... $data->keys()
            )
            ->values(
                ...$data->values()
            )
        ;

        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $entity = new Entity(
            $this->db->lastInsertId(),
            $ability
        );

        if (count($ability->getRules()) > 0) {
            $this->addRules($entity, $ability->getRules());
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function addRules(Entity $ability, array $rules)
    {
        if (count($rules) == 0) {
            return;
        }

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::ABILITY_RULES())
            ->columns(
                'ability_id',
                'rule_id'
            )
        ;
        foreach ($rules as $rule) {
            $query->values(
                $ability->id(),
                $rule->id()
            );
        }
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteRules(Entity $ability, array $rules)
    {
        if (count($rules) == 0) {
            return;
        }

        $ruleIds = array_map(fn($rule) => $rule->id(), $rules);

        $query = $this->db->createQueryFactory()
            ->delete((string) Tables::ABILITY_RULES())
            ->where(field('ability_id')->eq($ability->id()))
            ->andWhere(field('rule_id')->in(...$ruleIds))
        ;

        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function update(Entity $ability): void
    {
        $query = $this->db->createQueryFactory()
            ->update((string) Tables::ABILITIES())
            ->set(AbilityMapper::toPersistence($ability->domain()))
            ->where(field('id')->eq($ability->id()))
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @inheritDoc
     */
    private function createQuery(): AbilityQuery
    {
        return new AbilityDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     */
    public function getAll(
        ?AbilityQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection {
        $query ??= $this->createQuery();
        /* @var Collection $abilities */
        $abilities = $query->execute($limit, $offset);
        return $abilities->mapWithKeys(
            fn($item, $key) => [
                $key => new Entity((int) $key, AbilityMapper::toDomain($item))
            ]
        );
    }
}
