<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Users\Infrastructure\AbilitiesTable;
use Kwai\Modules\Users\Infrastructure\Mappers\AbilityDTO;
use Kwai\Modules\Users\Infrastructure\Mappers\RuleDTO;
use Kwai\Modules\Users\Infrastructure\RuleActionsTable;
use Kwai\Modules\Users\Infrastructure\RulesTable;
use Kwai\Modules\Users\Infrastructure\RuleSubjectsTable;
use Kwai\Modules\Users\Infrastructure\UserAbilitiesTable;
use Kwai\Modules\Users\Repositories\UserAbilityQuery;
use function Latitude\QueryBuilder\on;

/**
 * Class UserAbilityDatabaseQuery
 */
class UserAbilityDatabaseQuery extends DatabaseQuery implements UserAbilityQuery
{
    private AbilityDatabaseQuery $abilityQuery;

    public function __construct(Connection $db)
    {
        $this->abilityQuery = new AbilityDatabaseQuery($db);
        parent::__construct($db, UserAbilitiesTable::column('user_id'));
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query = $this->abilityQuery->query;
        $this->query->join(
            (string) UserAbilitiesTable::name(),
            on(
                AbilitiesTable::column('id'),
                UserAbilitiesTable::column('ability_id')
            )
        );
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            ...UserAbilitiesTable::aliases(),
            ...$this->abilityQuery->getColumns()
        ];
    }

    public function filterByUser(int ...$userIds): UserAbilityQuery
    {
        $this->query->andWhere(
            UserAbilitiesTable::field('user_id')->in(...$userIds)
        );
        return $this;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection<AbilityDTO>
     * @throws QueryException
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $users = new Collection();

        foreach ($rows as $row) {
            $user_id = UserAbilitiesTable::createFromRow($row)->user_id;
            $ability = AbilitiesTable::createFromRow($row);

            $userAbilities = $users->when(
                !$users->has($user_id),
                fn ($collection) => $collection->put($user_id, new Collection())
            )->get($user_id);

            if (!$userAbilities->has($ability->id)) {
                $userAbilities->put($ability->id, new AbilityDTO($ability));
            }

            $ruleDTO = new RuleDTO(
                rule: RulesTable::createFromRow($row),
                action: RuleActionsTable::createFromRow($row),
                subject: RuleSubjectsTable::createFromRow($row)
            );
            $userAbilities
                ->get($ability->id)
                ->rules
                ->push($ruleDTO)
            ;
        }

        return $users;
    }
}
