<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\UniqueId;
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
    private function createQuery()
    {
        $aliasFn = Tables::AUTHORS()->getAliasFn();
        return $this->db->createQueryFactory()
            ->select(
                $aliasFn('id'),
                $aliasFn('first_name'),
                $aliasFn('last_name')
            )
            ->from((string) Tables::AUTHORS())
        ;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->createQuery()
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

    /**
     * @inheritDoc
     */
    public function getByUniqueId(UniqueId $uuid): Entity
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->createQuery()
            ->where(field(Tables::AUTHORS()->uuid)->eq($uuid))
        ;

        try {
            $user = $this->db->execute($query)->fetch();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($user) {
            return AuthorMapper::toDomain(
                Tables::AUTHORS()->createColumnFilter()->filter($user)
            );
        }
        throw new AuthorNotFoundException($uuid);
    }
}
