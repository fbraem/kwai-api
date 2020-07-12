<?php
/**
 * @package Pages
 * @subpackage Repositories
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Pages\Domain\Application;

/**
 * Interface ApplicationRepository
 */
interface ApplicationRepository
{
    /**
     * @param int $id
     * @return Entity<Application>
     * @throws ApplicationNotFoundException
     * @throws RepositoryException
     */
    public function getById(int $id): Entity;
}
