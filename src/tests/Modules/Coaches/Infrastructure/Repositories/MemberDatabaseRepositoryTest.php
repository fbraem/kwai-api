<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Coaches\Domain\Exceptions\MemberNotFoundException;
use Kwai\Modules\Coaches\Infrastructure\Repositories\MemberDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get a member with a given id', function () use ($context) {
    $repo = new MemberDatabaseRepository($context->db);
    try {
        $user = $repo->getById(1);
        expect($user)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    } catch (QueryException $e) {
        $this->fail((string) $e);
    } catch (MemberNotFoundException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available');
