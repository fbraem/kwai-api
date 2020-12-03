<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kwai\Core\Infrastructure\Database\ColumnCollection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingDefinitionMapper;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use Kwai\Modules\Trainings\Repositories\TrainingDefinitionQuery;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class TrainingDefinitionDatabaseQuery
 */
class TrainingDefinitionDatabaseQuery extends DatabaseQuery implements TrainingDefinitionQuery
{
    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null)
    {
        $this->db->asArray();
        $rows = LazyCollection::make(
            parent::walk($limit, $offset)
        );
        $this->db->asObject();

        $filters = new Collection([
            Tables::TRAINING_DEFINITIONS()->getAliasPrefix(),
            Tables::USERS()->getAliasPrefix()
        ]);

        $definitions = new Collection();
        foreach ($rows as $row) {
            [ $definition, $creator ] =
                (new ColumnCollection($row))->filter($filters);
            $definition->put('creator', $creator);
            $definitions->put(
                $definition['id'],
                TrainingDefinitionMapper::toDomain($definition)
            );
        }
        return $definitions;
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::TRAINING_DEFINITIONS()->id)->eq($id)
        );
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
            ->from((string) Tables::TRAINING_DEFINITIONS())
            ->join(
                (string) Tables::USERS(),
                on(Tables::USERS()->id, Tables::TRAINING_DEFINITIONS()->user_id)
            )
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $definitionAliasFn = Tables::TRAINING_DEFINITIONS()->getAliasFn();
        $creatorAliasFn = Tables::USERS()->getAliasFn();

        return [
            $definitionAliasFn('id'),
            $definitionAliasFn('name'),
            $definitionAliasFn('description'),
            $definitionAliasFn('season_id'),
            $definitionAliasFn('team_id'),
            $definitionAliasFn('weekday'),
            $definitionAliasFn('start_time'),
            $definitionAliasFn('end_time'),
            $definitionAliasFn('time_zone'),
            $definitionAliasFn('active'),
            $definitionAliasFn('location'),
            $definitionAliasFn('remark'),
            $definitionAliasFn('user_id'),
            $definitionAliasFn('created_at'),
            $definitionAliasFn('updated_at'),
            $creatorAliasFn('id'),
            $creatorAliasFn('first_name'),
            $creatorAliasFn('last_name')
        ];
    }
}
