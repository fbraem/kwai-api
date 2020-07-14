<?php
/**
 * @package Pages
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\Pages\Infrastructure\Mappers\AuthorMapper;
use Kwai\Modules\Pages\Infrastructure\Tables;
use Kwai\Modules\Pages\Repositories\AuthorRepository;
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

        try {
            $row = $this->db->execute(
                $query
            )->fetch();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if (!$row) {
            throw new AuthorNotFoundException($id);
        }

        return AuthorMapper::toDomain(
            Tables::AUTHORS()->createColumnFilter()->filter($row)
        );
    }
}
