<?php
declare(strict_types=1);

namespace Tests\Modules\News\UseCases;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\UseCases\GetArchive;
use Kwai\Modules\News\UseCases\GetArchiveCommand;
use Tests\Context;

$context = Context::createContext();

it('can get an story archive', function () use ($context) {
    $command = new GetArchiveCommand();
    try {
        $archive = (new GetArchive(
            new StoryDatabaseRepository($context->db)
        ))($command);

        expect($archive)
            ->toBeGreaterThan(0)
        ;
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->skip($context->db->getDriver() == 'sqlite', 'sqlite does not support YEAR function')
;
