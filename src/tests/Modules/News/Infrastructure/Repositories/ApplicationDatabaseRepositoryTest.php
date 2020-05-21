<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\CategoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\News\Repositories\ApplicationRepository;
use Tests\DatabaseTestCase;

class ApplicationDatabaseRepositoryTest extends DatabaseTestCase
{
    private ApplicationRepository $repo;

    public function setUp(): void
    {
        $this->repo = new ApplicationDatabaseRepository(self::$db);
    }

    public function testGetById()
    {
        try {
            $category = $this->repo->getById(1);
            $this->assertInstanceOf(
                Entity::class,
                $category
            );
        } catch (RepositoryException $e) {
            $this->assertTrue(false, (string) $e);
        } catch (CategoryNotFoundException $e) {
            $this->assertTrue(false, (string) $e);
        }
    }
}
