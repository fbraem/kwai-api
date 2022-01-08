<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Users\Infrastructure\AbilitiesTableSchema;
use Kwai\Modules\Users\Infrastructure\RulesTableSchema;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Infrastructure\UserAbilitiesTableSchema;
use Kwai\Modules\Users\Repositories\UserAbilityQuery;
use function Latitude\QueryBuilder\field;
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
        parent::__construct($db, UserAbilitiesTableSchema::column('user_id'));
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query = $this->abilityQuery->query;
        $this->query->join(
            (string) UserAbilitiesTableSchema::name(),
            on(
                AbilitiesTableSchema::column('id'),
                UserAbilitiesTableSchema::column('ability_id')
            )
        );
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            ...UserAbilitiesTableSchema::aliases(),
            ...$this->abilityQuery->getColumns()
        ];
    }

    public function filterByUser(int ...$userIds): UserAbilityQuery
    {
        $this->query->andWhere(
            UserAbilitiesTableSchema::field('user_id')->in(...$userIds)
        );
        return $this;
    }

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $users = new Collection();

        foreach ($rows as $row) {
            $user_id = UserAbilitiesTableSchema::createFromRow($row)->user_id;
            $ability = AbilitiesTableSchema::createFromRow($row);
            $rule = RulesTableSchema::createFromRow($row);

            $userAbilities = $users->when(
                !$users->has($user_id),
                fn ($collection) => $collection->put($user_id, new Collection())
            )->get($user_id);

            if (!$userAbilities->has($ability->id)) {
                $ability->put('rules', new Collection());
                $userAbilities->put($ability->id, $ability);
            }
            $userAbilities
                ->get($ability->id)
                ->get('rules')
                ->push($rule)
            ;
        }

        return $users;
    }
}
