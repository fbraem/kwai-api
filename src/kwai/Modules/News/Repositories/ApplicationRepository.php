<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Applications\Domain\Exceptions\ApplicationNotFoundException;

/**
 * Interface CategoryRepository
 */
interface ApplicationRepository
{
    /**
     * @param int $id
     * @return Entity
     * @throws ApplicationNotFoundException
     * @throws RepositoryException
     */
    public function getById(int $id): Entity;
}
