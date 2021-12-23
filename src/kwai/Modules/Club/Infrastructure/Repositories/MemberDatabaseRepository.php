<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Modules\Club\Domain\Exceptions\MemberNotFoundException;
use Kwai\Modules\Club\Infrastructure\Mappers\MemberMapper;
use Kwai\Modules\Club\Repositories\MemberRepository;

/**
 * Class MemberDatabaseRepository
 */
class MemberDatabaseRepository extends DatabaseRepository implements MemberRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            fn ($item) => MemberMapper::toDomain($item)
        );
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): MemberDatabaseQuery
    {
        return new MemberDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery()->filterId($id);
        $members = $this->getAll($query);
        if ($members->isEmpty()) {
            throw new MemberNotFoundException($id);
        }

        return $members->first();
    }
}
