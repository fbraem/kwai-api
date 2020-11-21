<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use Kwai\Modules\Trainings\Repositories\TrainingQuery;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class TrainingDatabaseQuery
 */
class TrainingDatabaseQuery extends DatabaseQuery implements TrainingQuery
{
    /**
     * @inheritDoc
     * @noinspection PhpUndefinedFieldInspection
     */
    protected function initQuery(): void
    {
        $this->query
           ->from((string) Tables::TRAININGS())
            ->leftJoin(
                (string) Tables::TRAINING_DEFINITIONS(),
                on(Tables::TRAININGS()->definition_id, Tables::TRAINING_DEFINITIONS()->id)
            )
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $trainingAliasFn = Tables::TRAININGS()->getAliasFn();
        $definitionAliasFn = Tables::TRAINING_DEFINITIONS()->getAliasFn();

        return [
            $trainingAliasFn('id'),
            $trainingAliasFn('definition_id'),
            $trainingAliasFn('created_at'),
            $trainingAliasFn('updated_at'),
            $trainingAliasFn('start_date'),
            $trainingAliasFn('end_date'),
            $trainingAliasFn('time_zone'),
            $trainingAliasFn('active'),
            $trainingAliasFn('cancelled'),
            $trainingAliasFn('location'),
            $definitionAliasFn('id'),
            $definitionAliasFn('name')
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::TRAININGS()->id)->eq($id)
        );
    }
}
