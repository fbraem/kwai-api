<?php
declare(strict_types=1);

namespace Tests\Modules\News\UseCases;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\UseCases\GetArchive;
use Kwai\Modules\News\UseCases\GetArchiveCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can get an story archive', function () {
    $command = new GetArchiveCommand();
    try {
        $archive = (new GetArchive(
            new StoryDatabaseRepository($this->db)
        ))($command);

        expect($archive)
            ->toBeGreaterThan(0)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
    ->skip(fn() => $this->isDatabaseDriver('sqlite'), 'sqlite does not support YEAR function')
;
