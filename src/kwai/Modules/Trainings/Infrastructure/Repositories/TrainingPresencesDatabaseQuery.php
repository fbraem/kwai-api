<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Trainings\Infrastructure\Mappers\CreatorDTO;
use Kwai\Modules\Trainings\Infrastructure\Mappers\MemberDTO;
use Kwai\Modules\Trainings\Infrastructure\Mappers\PresenceDTO;
use Kwai\Modules\Trainings\Infrastructure\MembersTable;
use Kwai\Modules\Trainings\Infrastructure\PersonsTable;
use Kwai\Modules\Trainings\Infrastructure\TrainingPresencesTable;
use Kwai\Modules\Trainings\Infrastructure\UsersTable;
use function Latitude\QueryBuilder\on;

/**
 * Class TrainingPresencesDatabaseQuery
 *
 * Query class for querying presences of a training.
 */
class TrainingPresencesDatabaseQuery extends DatabaseQuery
{
    protected function initQuery(): void
    {
        $this->query
            ->from(TrainingPresencesTable::name())
            ->join(
                MembersTable::name(),
                on(
                    TrainingPresencesTable::column('member_id'),
                    MembersTable::column('id')
                )
            )
            ->join(
                PersonsTable::name(),
                on(
                    MembersTable::column('person_id'),
                    PersonsTable::column('id')
                )
            )
            ->join(
                UsersTable::name(),
                on(
                    UsersTable::column('id'),
                    TrainingPresencesTable::column('user_id')
                )
            )
        ;
    }

    /**
     * Get all teams for the given trainings
     *
     * @param int[] $ids
     */
    public function filterOnTrainings(array $ids)
    {
        $this->query->andWhere(
            TrainingPresencesTable::field('training_id')->in(...$ids)
        );
    }

    protected function getColumns(): array
    {
        return [
            ... TrainingPresencesTable::aliases(),
            ... MembersTable::aliases(),
            ... PersonsTable::aliases(),
            ... UsersTable::aliases()
        ];
    }

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $trainings = new Collection();
        foreach ($rows as $row) {
            $presence = TrainingPresencesTable::createFromRow($row);
            if (!$trainings->has($presence->training_id)) {
                $trainings->put($presence->training_id, new Collection());
            }
            $trainings->get($presence->training_id)->put(
                new PresenceDTO(
                    $presence,
                    new MemberDTO(
                        MembersTable::createFromRow($row),
                        PersonsTable::createFromRow($row)
                    ),
                    new CreatorDTO(
                        UsersTable::createFromRow($row)
                    )
                )
            );
        }
        return $trainings;
    }
}
