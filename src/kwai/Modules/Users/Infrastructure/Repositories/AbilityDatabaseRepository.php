<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseException;
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

        $entities = $this->getAll($query);
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

        try {
             $this->db->begin();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

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

            $entity = new Entity(
                $this->db->lastInsertId(),
                $ability
            );

            $this->insertRules($entity);
        } catch (QueryException $e) {
            try {
                $this->db->rollback();
            } catch (DatabaseException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
            throw new RepositoryException(__METHOD__, $e);
        }

        try {
             $this->db->commit();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        return $entity;
    }

    /**
     * Insert rules of the ability
     *
     * @param Entity $ability
     * @throws QueryException
     */
    private function insertRules(Entity $ability)
    {
        /* @var Collection $rules */
        /** @noinspection PhpUndefinedMethodInspection */
        $rules = $ability->getRules();
        if ($rules->count() === 0) {
            return;
        }

        $rules
            ->transform(
                fn (Entity $rule) => collect(['rule_id' => $rule->id()])
            )
            ->map(
                fn (Collection $item) => $item->put('ability_id', $ability->id())
            )
        ;
        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::ABILITY_RULES())
            ->columns(...$rules->first()->keys())
        ;
        $rules->each(
            fn (Collection $rule) => $query->values(... $rule->values())
        );

        $this->db->execute($query);
    }

    /**
     * @inheritDoc
     */
    public function update(Entity $ability): void
    {
        try {
            $this->db->begin();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $queryFactory = $this->db->createQueryFactory();
        $query = $queryFactory
            ->update((string) Tables::ABILITIES())
            ->set(AbilityMapper::toPersistence($ability->domain())->toArray())
            ->where(field('id')->eq($ability->id()))
        ;
        try {
            $this->db->execute($query);
            // Update rules
            // First delete all rules
            $this->db->execute(
                $queryFactory
                  ->delete((string) Tables::ABILITY_RULES())
                  ->where(field('ability_id')->eq($ability->id()))
            );
            // Insert the rules again
            $this->insertRules($ability);
        } catch (QueryException $e) {
            try {
                $this->db->rollback();
            } catch (DatabaseException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
            throw new RepositoryException(__METHOD__, $e);
        }

        try {
            $this->db->commit();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): AbilityQuery
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
        try {
            $abilities = $query->execute($limit, $offset);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        return $abilities->mapWithKeys(
            fn($item, $key) => [
                $key => new Entity((int) $key, AbilityMapper::toDomain($item))
            ]
        );
    }
}
