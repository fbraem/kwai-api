<?php
/**
 * Testcase for TableData
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Kwai\Core\Infrastructure\DefaultTable;
use Kwai\Core\Infrastructure\AliasTable;

final class TableTest extends TestCase
{
    public function testCreateDefaultTable(): void
    {
        $table = new class('users', ['id']) extends DefaultTable {
        };
        $this->assertEquals(
            $table->from(),
            'users'
        );
        $this->assertArrayHasKey('users.id', $table->alias());
        $this->assertContains('users_id', $table->alias());
    }

    public function testCreateAliasTable(): void
    {
        $table = new AliasTable(
            new class('users', ['id']) extends DefaultTable {
            },
            'u'
        );
        $this->assertEquals(
            $table->from(),
            ['users' => 'u']
        );
        $this->assertArrayHasKey('u.id', $table->alias());
        echo var_dump($table->alias()), PHP_EOL;
        $this->assertContains('u_id', $table->alias());
    }
}
