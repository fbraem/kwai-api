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
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingDefinitionMapper;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use Kwai\Modules\Trainings\Repositories\TrainingDefinitionQuery;
use function Latitude\QueryBuilder\field;

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

        $definitions = new Collection();
        foreach ($rows as $row) {
            [ $definition ] = (new ColumnCollection($row))->filter(
                new Collection([
                    Tables::TRAINING_DEFINITIONS()->getAliasPrefix()
                ])
            );
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
        $this->query
            ->from((string) Tables::TRAINING_DEFINITIONS())
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $definitionAliasFn = Tables::TRAINING_DEFINITIONS()->getAliasFn();

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
            $definitionAliasFn('updated_at')
        ];
    }
}
