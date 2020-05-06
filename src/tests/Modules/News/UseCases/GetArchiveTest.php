<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\News\UseCases;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\UseCases\GetArchive;
use Kwai\Modules\News\UseCases\GetArchiveCommand;
use Tests\DatabaseTestCase;

class GetArchiveTest extends DatabaseTestCase
{
    public function testGetArchive(): void
    {
        $command = new GetArchiveCommand();
        try {
            $archive = (new GetArchive(
                new StoryDatabaseRepository(self::$db)
            ))($command);
            self::assertGreaterThan(0, count($archive), 'No archive found!');
        } catch (RepositoryException $e) {
            self::assertTrue(false, (string) $e);
        }
    }
}
