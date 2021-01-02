<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\News\Infrastructure\Mappers\ApplicationMapper;
use Kwai\Modules\News\Infrastructure\Tables;
use Kwai\Modules\News\Repositories\ApplicationRepository;
use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\field;

/**
 * Class CategoryDatabaseRepository
 */
class ApplicationDatabaseRepository extends DatabaseRepository implements ApplicationRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->createBaseQuery()
            ->where(field(Tables::APPLICATIONS()->id)->eq($id))
        ;

        try {
            $row = collect($this->db->execute(
                $query
            )->fetch());
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if (!$row) {
            throw new ApplicationNotFoundException($id);
        }

        return new Entity(
            (int) $row->get('id'),
            ApplicationMapper::toDomain($row)
        );
    }

    /**
     * Create the base query
     *
     * @return SelectQuery
     */
    private function createBaseQuery(): SelectQuery
    {
        return $this->db->createQueryFactory()
            ->select(
                'id',
                'title',
                'name'
            )
            ->from((string) Tables::APPLICATIONS())
        ;
    }
}
