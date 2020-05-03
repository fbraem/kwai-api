<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Repositories\QueryException;
use Kwai\Modules\News\Domain\Exceptions\CategoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Mappers\CategoryMapper;
use Kwai\Modules\News\Infrastructure\Tables;
use Kwai\Modules\News\Repositories\CategoryRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class CategoryDatabaseRepository
 */
class CategoryDatabaseRepository extends DatabaseRepository implements CategoryRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $aliasFn = Tables::CATEGORIES()->getAliasFn();

        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->db->createQueryFactory()
            ->select(
                $aliasFn('id'),
                $aliasFn('name')
            )
            ->from((string) Tables::CATEGORIES())
            ->where(field(Tables::CATEGORIES()->id)->eq($id))
        ;

        $compiledQuery = $query->compile();
        try {
            $row = $this->db->execute(
                $compiledQuery
            )->fetch();
        } catch (DatabaseException $e) {
            throw new QueryException($compiledQuery->sql(), $e);
        }

        if (!$row) {
            throw new CategoryNotFoundException($id);
        }

        return CategoryMapper::toDomain(
            Tables::CATEGORIES()->createColumnFilter()->filter($row)
        );
    }
}
