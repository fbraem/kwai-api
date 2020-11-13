<?php
/**
 * @package
 * @subpackage
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
        $aliasFn = Tables::APPLICATIONS()->getAliasFn();

        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->db->createQueryFactory()
            ->select(
                $aliasFn('id'),
                $aliasFn('title'),
                $aliasFn('name')
            )
            ->from((string) Tables::APPLICATIONS())
            ->where(field(Tables::APPLICATIONS()->id)->eq($id))
        ;

        try {
            $row = $this->db->execute(
                $query
            )->fetch();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if (!$row) {
            throw new ApplicationNotFoundException($id);
        }

        return ApplicationMapper::toDomain(
            Tables::APPLICATIONS()->createColumnFilter()->filter($row)
        );
    }
}
