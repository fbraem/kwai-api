<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingMapper;
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
            ->leftJoin(
                (string) Tables::TRAINING_CONTENTS(),
                on(Tables::TRAINING_CONTENTS()->training_id, Tables::TRAININGS()->id)
            )
            ->leftJoin(
                (string) Tables::USERS(),
                on(Tables::USERS()->id, Tables::TRAINING_CONTENTS()->user_id)
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
        $contentAliasFn = Tables::TRAINING_CONTENTS()->getAliasFn();
        $creatorAliasFn = Tables::USERS()->getAliasFn();

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
            $definitionAliasFn('name'),
            $contentAliasFn('locale'),
            $contentAliasFn('format'),
            $contentAliasFn('title'),
            $contentAliasFn('content'),
            $contentAliasFn('summary'),
            $contentAliasFn('created_at'),
            $contentAliasFn('updated_at'),
            $creatorAliasFn('id'),
            $creatorAliasFn('first_name'),
            $creatorAliasFn('last_name')
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

    public function execute(?int $limit = null, ?int $offset = null)
    {
        $rows = parent::execute($limit, $offset);
        if (count($rows) === 0) {
            return [];
        }

        $trainingColumnFilter = Tables::TRAININGS()->createColumnFilter();
        $definitionColumnFilter = Tables::TRAINING_DEFINITIONS()->createColumnFilter();
        $contentColumnFilter = Tables::TRAINING_CONTENTS()->createColumnFilter();
        $creatorColumnFilter = Tables::USERS()->createColumnFilter();

        // Build the training object. There can be multiple rows for one
        // training (when there are multiple text content rows).
        $trainings = [];
        $trainingIdColumn = Tables::TRAININGS()->getAlias('id');
        $definitionIdColumn = Tables::TRAINING_DEFINITIONS()->getAlias('id');
        foreach ($rows as $row) {
            if (isset($trainings[$row->$trainingIdColumn])) {
                $training = $trainings[$row->$trainingIdColumn];
            } else {
                $training = $trainingColumnFilter->filter($row);
                if (isset($row->$definitionIdColumn)) {
                    $training->definition = $definitionColumnFilter->filter($row);
                }
                $training->contents = [];
                $trainings[$training->id] = $training;
            }
            $content = $contentColumnFilter->filter($row);
            $content->creator = $creatorColumnFilter->filter($row);
            $training->contents[] = $content;
        }

        $result = [];
        foreach ($trainings as $training) {
            $result[$training->id] = TrainingMapper::toDomain($training);
        }

        return $result;
    }
}
