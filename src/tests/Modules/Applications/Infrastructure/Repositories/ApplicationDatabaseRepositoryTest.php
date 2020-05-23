<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\Applications\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Applications\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Applications\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Applications\Repositories\ApplicationRepository;
use Tests\DatabaseTestCase;

class ApplicationDatabaseRepositoryTest extends DatabaseTestCase
{
    private ApplicationRepository $repo;

    public function setUp(): void
    {
        $this->repo = new ApplicationDatabaseRepository(self::$db);
    }

    public function testGetById(): void
    {
        try {
            $application = $this->repo->getById(1);
            $this->assertInstanceOf(Entity::class, $application);
        } catch (RepositoryException $e) {
            $this->assertTrue(false, strval($e));
        } catch (ApplicationNotFoundException $e) {
            $this->assertTrue(false, strval($e));
        }
    }
}
