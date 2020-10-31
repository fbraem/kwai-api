<?php
/**
 * Testcase for Database
 */
declare(strict_types=1);

namespace Tests\Core\Domain\Infrastructure;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\QueryException;

use Latitude\QueryBuilder\QueryFactory;

const DB_MEMORY = 'sqlite::memory:';

it('can instantiate a database', function () {
    $db = new Connection(DB_MEMORY);
    expect($db)->toBeInstanceOf(Connection::class);
});

it('throws an exception for an invalid connection', function () {
    new Connection('sqlite');
})->throws(DatabaseException::class);

it('can create a QueryFactory', function () {
    $db = new Connection(DB_MEMORY);
    $qf = $db->createQueryFactory();
    expect($qf)->toBeInstanceOf(QueryFactory::class);
});

it('can execute a query', function () {
    $db = new Connection(DB_MEMORY);
    $qf = $db->createQueryFactory();
    $query = $qf
        ->select('name', 'type')
        ->from('sqlite_master')
    ;
    try {
        $stmt = $db->execute($query);
        $result = $stmt->fetchAll();
        expect($result)->toBeArray();
    } catch (QueryException $e) {
    }
});

it('throws an exception for an invalid query', function () {
    $db = new Connection(DB_MEMORY);
    $qf = $db->createQueryFactory();
    $query = $qf
        ->select()
        ->from('')
    ;
    /** @noinspection PhpUnhandledExceptionInspection */
    $db->execute($query);
})->throws(QueryException::class);
