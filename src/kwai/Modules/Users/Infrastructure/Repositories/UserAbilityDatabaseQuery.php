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
        /** @noinspection PhpUndefinedFieldInspection */
        parent::__construct($db, Tables::USERS()->id);
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query = $this->abilityQuery->query;
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->join(
            (string) Tables::USER_ABILITIES(),
            on(
                Tables::ABILITIES()->id,
                Tables::USER_ABILITIES()->ability_id
            )
        );
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            Tables::USER_ABILITIES()->alias('user_id'),
            ...$this->abilityQuery->getColumns()
        ];
    }

    public function filterByUser(int ...$userIds): UserAbilityQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::USER_ABILITIES()->user_id)->in(...$userIds)
        );
        return $this;
    }

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $users = new Collection();
        $filters = new Collection([
            Tables::USER_ABILITIES()->getAliasPrefix(),
            Tables::ABILITIES()->getAliasPrefix(),
            Tables::RULES()->getAliasPrefix()
        ]);

        foreach ($rows as $row) {
            [
                $user,
                $ability,
                $rule
            ] = $row->filterColumns($filters);
            /** @noinspection PhpUndefinedMethodInspection */
            $user = $users->nest($user->get('user_id'));
            if (!$user->has($ability->get('id'))) {
                $ability->put('rules', new Collection());
                $user->put($ability->get('id'), $ability);
            }
            $user
                ->get($ability->get('id'))
                ->get('rules')
                ->push($rule)
            ;
        }

        return $users;
    }
}
