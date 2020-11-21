<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use Kwai\Modules\Trainings\Repositories\ContentQuery;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class ContentDatabaseQuery
 */
class ContentDatabaseQuery extends DatabaseQuery implements ContentQuery
{
    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
            ->from((string) Tables::TRAINING_CONTENTS())
            ->leftJoin(
                (string) Tables::USERS(),
                on(
                    Tables::TRAINING_CONTENTS()->user_id,
                    Tables::USERS()->id
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    public function filterIds(array $ids): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->where(
            field(Tables::TRAINING_CONTENTS()->training_id)->in(...$ids)
        );
    }

    /**
     * @inheritDoc
     * @return object[]
     */
    public function execute(?int $limit = null, ?int $offset = null): array
    {
        $rows = parent::execute($limit, $offset);
        $contentFilter = Tables::TRAINING_CONTENTS()->createColumnFilter();
        $authorFilter = Tables::USERS()->createColumnFilter();

        $result = [];
        foreach ($rows as $row) {
            $content = $contentFilter->filter($row);
            $content->author = $authorFilter->filter($row);
            if (! isset($result[$row->id])) {
                $result[$row->id] = [];
            }
            $result[$row->id][] = $content;
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasFn = Tables::TRAINING_CONTENTS()->getAliasFn();
        $aliasAuthorFn = Tables::USERS()->getAliasFn();

        /** @noinspection PhpUndefinedFieldInspection */
        return [
            alias(Tables::TRAINING_CONTENTS()->training_id, 'id'),
            $aliasFn('locale'),
            $aliasFn('format'),
            $aliasFn('title'),
            $aliasFn('content'),
            $aliasFn('summary'),
            $aliasFn('user_id'),
            $aliasFn('created_at'),
            $aliasFn('updated_at'),
            $aliasAuthorFn('id'),
            $aliasAuthorFn('first_name'),
            $aliasAuthorFn('last_name')
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterCreator(int $id): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(field(Tables::TRAINING_CONTENTS()->user_id)->eq($id));
    }
}
