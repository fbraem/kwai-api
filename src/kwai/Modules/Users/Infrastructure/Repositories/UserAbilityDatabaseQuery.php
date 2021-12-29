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
use Kwai\Modules\Users\Infrastructure\Tables;
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
        parent::__construct($db, Tables::USERS->column('id'));
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query = $this->abilityQuery->query;
        $this->query->join(
            (string) Tables::USER_ABILITIES->value,
            on(
                Tables::ABILITIES->column('id'),
                Tables::USER_ABILITIES->column('ability_id')
            )
        );
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            Tables::USER_ABILITIES->alias('user_id'),
            ...$this->abilityQuery->getColumns()
        ];
    }

    public function filterByUser(int ...$userIds): UserAbilityQuery
    {
        $this->query->andWhere(
            Tables::USER_ABILITIES->field('user_id')->in(...$userIds)
        );
        return $this;
    }

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $users = new Collection();

        foreach ($rows as $row) {
            $user_id = Tables::USER_ABILITIES->collect($row)->get('user_id');
            $ability = Tables::ABILITIES->collect($row);
            $rule = Tables::RULES->collect($row);

            $userAbilities = $users->when(
                !$users->has($user_id),
                fn ($collection) => $collection->put($user_id, new Collection())
            )->get($user_id);

            if (!$userAbilities->has($ability->get('id'))) {
                $ability->put('rules', new Collection());
                $userAbilities->put($ability->get('id'), $ability);
            }
            $userAbilities
                ->get($ability->get('id'))
                ->get('rules')
                ->push($rule)
            ;
        }

        return $users;
    }
}
