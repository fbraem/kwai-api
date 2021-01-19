<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Pages\Infrastructure\Mappers\ApplicationMapper;
use Kwai\Modules\Pages\Infrastructure\Tables;
use Kwai\Modules\Pages\Repositories\ApplicationRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class ApplicationDatabaseRepository
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
            $row = collect($this->db->execute(
                $query
            )->fetch());
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if (!$row) {
            throw new ApplicationNotFoundException($id);
        }

        [ $application ] = $row->filterColumns([
            Tables::APPLICATIONS()->getAliasPrefix()
        ]);

        return new Entity(
            (int) $application->get('id'),
            ApplicationMapper::toDomain($application)
        );
    }
}
