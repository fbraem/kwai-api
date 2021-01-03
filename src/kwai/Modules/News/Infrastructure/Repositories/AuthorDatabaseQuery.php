<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\News\Infrastructure\Tables;
use Kwai\Modules\News\Repositories\AuthorQuery;
use function Latitude\QueryBuilder\field;

/**
 * Class AuthorDatabaseQuery
 */
class AuthorDatabaseQuery extends DatabaseQuery implements AuthorQuery
{

    /**
     * @inheritDoc
     */
    public function filterIds(int ...$ids): AuthorQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::AUTHORS()->id)->in(...$ids)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterUniqueId(UniqueId $id): AuthorQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::AUTHORS()->uuid)->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query->from((string) Tables::AUTHORS());
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasFn = Tables::AUTHORS()->getAliasFn();

        return [
            $aliasFn('id'),
            $aliasFn('first_name'),
            $aliasFn('last_name')
        ];
    }
}
