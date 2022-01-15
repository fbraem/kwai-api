<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\AbilityNotFoundException;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\Modules\Users\Infrastructure\AbilitiesTable;
use Kwai\Modules\Users\Infrastructure\AbilityRulesTable;
use Kwai\Modules\Users\Infrastructure\Mappers\AbilityDTO;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleDTO;
use Kwai\Modules\Users\Repositories\AbilityQuery;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class AbilityDatabaseRepository
 *
 * Ability repository for read/write Ability entity from/to a database.
 */
final class AbilityDatabaseRepository extends DatabaseRepository implements AbilityRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            static fn(AbilityDTO $dto) => $dto->createEntity()
        );
    }

    /**
     * @inheritDoc
     * @return Entity<Ability>
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery()->filterByIds($id);

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
        $dto = (new AbilityDTO())->persist($ability);

        try {
             $this->db->begin();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $data = $dto->ability
            ->collect()
            ->forget('id')
        ;
        $query = $this->db->createQueryFactory()
            ->insert(AbilitiesTable::name())
            ->columns(... $data->keys())
            ->values(...$data->values())
        ;

        try {
            $this->db->execute($query);

            $entity = new Entity(
                $this->db->lastInsertId(),
                $ability
            );

            $this->insertRules($entity->id(), $dto->rules);
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
     * @param int                 $abilityId
     * @param Collection<RuleDTO> $rules
     * @throws QueryException
     */
    private function insertRules(int $abilityId, Collection $rules)
    {
        if ($rules->count() === 0) {
            return;
        }

        $rules
            ->transform(
                fn (RuleDTO $dto) => collect(['rule_id' => $dto->rule->id])
            )
            ->map(
                fn (Collection $item) => $item->put('ability_id', $abilityId)
            )
        ;
        $query = $this->db->createQueryFactory()
            ->insert(AbilityRulesTable::name())
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

        $dto = (new AbilityDTO())->persist($ability->domain());
        $data = $dto->ability
            ->collect()
            ->forget('id')
        ;

        $queryFactory = $this->db->createQueryFactory();
        $query = $queryFactory
            ->update(AbilitiesTable::name())
            ->set($data->toArray())
            ->where(field('id')->eq($ability->id()))
        ;
        try {
            $this->db->execute($query);
            // Update rules
            // First delete all rules
            $this->db->execute(
                $queryFactory
                  ->delete(AbilityRulesTable::name())
                  ->where(field('ability_id')->eq($ability->id()))
            );
            // Insert the rules again
            $this->insertRules($ability->id(), $dto->rules);
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
}
