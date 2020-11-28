<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Core\Infrastructure\Database\ColumnCollection;
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
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            Tables::TRAININGS()->getColumn('id')
        );
    }

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
        $this->db->asArray();
        $rows = LazyCollection::make(
            parent::walk($limit, $offset)
        );
        $this->db->asObject();

        $trainings = new Collection();
        $filters = new Collection([
            Tables::TRAININGS()->getAliasPrefix(),
            Tables::TRAINING_DEFINITIONS()->getAliasPrefix(),
            Tables::TRAINING_CONTENTS()->getAliasPrefix(),
            Tables::USERS()->getAliasPrefix()
        ]);
        foreach ($rows as $row) {
            $rowCollection = new ColumnCollection($row);
            [
                $training,
                $definition,
                $content,
                $user
            ] = $rowCollection->filter($filters);

            if (!$trainings->has($training['id'])) {
                $trainings->put($training['id'], $training);
                if ($definition->has('id')) {
                    $training->put('definition', $definition);
                }
                $training->put('contents', new Collection());
            }
            $content['creator'] = $user;
            $trainings[$training['id']]['contents']->push($content);
        }

        if ($rows->isEmpty()) {
            return [];
        }

        $trainingCoachQuery = new TrainingCoachDatabaseQuery($this->db);
        $trainingCoachQuery->filterOnTrainings($trainings->keys()->toArray());
        $trainingCoaches = $trainingCoachQuery->execute();
        foreach ($trainingCoaches as $trainingId => $trainingCoach) {
            $trainings[$trainingId]->put('coaches', $trainingCoach);
        }

        $result = [];
        foreach ($trainings as $training) {
            $result[$training['id']] = TrainingMapper::toDomain($training);
        }

        return $result;
    }
}
