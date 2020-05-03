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
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Infrastructure\Mappers\AuthorMapper;
use Kwai\Modules\News\Infrastructure\Tables;
use Kwai\Modules\News\Repositories\AuthorRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class AuthorDatabaseRepository
 */
class AuthorDatabaseRepository extends DatabaseRepository implements AuthorRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $aliasFn = Tables::AUTHORS()->getAliasFn();

        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->db->createQueryFactory()
            ->select(
                $aliasFn('id'),
                $aliasFn('first_name'),
                $aliasFn('last_name')
            )
            ->from((string) Tables::AUTHORS())
            ->where(field(Tables::AUTHORS()->id)->eq($id))
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
            throw new AuthorNotFoundException($id);
        }

        return AuthorMapper::toDomain($row);
    }
}
