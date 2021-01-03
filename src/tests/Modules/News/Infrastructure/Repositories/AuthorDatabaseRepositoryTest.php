<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\News\Infrastructure\Repositories\AuthorDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get an author', function () use ($context) {
    $repo = new AuthorDatabaseRepository($context->db);
    try {
        $author = $repo->getById(1);
        expect($author)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
