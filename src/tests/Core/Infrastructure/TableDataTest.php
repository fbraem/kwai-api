<?php
/**
 * Testcase for TableData
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Kwai\Core\Infrastructure\TableData;

final class TableDataTest extends TestCase
{
    public function testCreateTableData(): void
    {
        $this->assertInstanceOf(
            TableData::class,
            new TableData(new stdClass)
        );
    }

    public function testCreateTableDataWithData(): void
    {
        $object = (object) [
            'column_1' => 1,
            'column_2' => 2
        ];
        $t = new TableData($object);
        $this->assertInstanceOf(
            TableData::class,
            $t
        );
    }

    public function testCreateTableDataWithPrefix(): void
    {
        $object = (object) [
            'test_column_1' => 1,
            'test_column_2' => 2,
            'column_3' => 3
        ];
        $t = new TableData($object, 'test_');
        echo var_dump($t->column_2), PHP_EOL;
        echo var_dump($t), PHP_EOL;
        $this->assertInstanceOf(
            TableData::class,
            $t
        );
    }
}
