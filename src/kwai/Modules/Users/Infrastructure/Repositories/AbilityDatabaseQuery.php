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
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\AbilityQuery;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class AbilityDatabaseQuery
 */
class AbilityDatabaseQuery extends DatabaseQuery implements AbilityQuery
{
    public function __construct(Connection $db)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        parent::__construct($db, Tables::ABILITIES()->id);
    }

    /**
     * @inheritDoc
     */
    public function filterById(int $id): AbilityQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::ABILITIES()->id)->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
            ->from((string) Tables::ABILITIES())
            ->leftJoin(
                (string) Tables::ABILITY_RULES(),
                on(
                    Tables::ABILITIES()->id,
                    Tables::ABILITY_RULES()->ability_id
                )
            )
            ->leftJoin(
                (string) Tables::RULES(),
                on(
                    Tables::ABILITY_RULES()->rule_id,
                    Tables::RULES()->id
                )
            )
            ->leftJoin(
                (string) Tables::RULE_ACTIONS(),
                on(
                    Tables::RULES()->action_id,
                    Tables::RULE_ACTIONS()->id
                )
            )
            ->leftJoin(
                (string) Tables::RULE_SUBJECTS(),
                on(
                    Tables::RULES()->subject_id,
                    Tables::RULE_SUBJECTS()->id
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasFn = Tables::ABILITIES()->getAliasFn();
        $aliasRuleFn = Tables::RULES()->getAliasFn();
        /** @noinspection PhpUndefinedFieldInspection */
        return [
            $aliasFn('id'),
            $aliasFn('name'),
            $aliasFn('remark'),
            $aliasRuleFn('id'),
            $aliasRuleFn('name'),
            $aliasRuleFn('remark'),
            $aliasRuleFn('created_at'),
            $aliasRuleFn('updated_at'),
            $aliasFn('created_at'),
            $aliasFn('updated_at'),
            alias(
                Tables::RULE_ACTIONS()->name,
                Tables::RULES()->getAlias('action')
            ),
            alias(
                Tables::RULE_SUBJECTS()->name,
                Tables::RULES()->getAlias('subject')
            )
        ];
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $abilities = new Collection();
        $filters = new Collection([
            Tables::ABILITIES()->getAliasPrefix(),
            Tables::RULES()->getAliasPrefix()
        ]);

        foreach ($rows as $row) {
            [
                $ability,
                $rule
            ] = $row->filterColumns($filters);
            if (!$abilities->has($ability->get('id'))) {
                $abilities->put($ability['id'], $ability);
                $ability->put('rules', new Collection());
            }
            $abilities
                ->get($ability->get('id'))
                ->get('rules')
                ->push($rule);
        }

        return $abilities;
    }
}
