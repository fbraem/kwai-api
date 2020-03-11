<?php
/**
 * @package Kwai
 * @subpackage Core
 * @author Franky Braem
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Database;

use Latitude\QueryBuilder\ExpressionInterface;
use stdClass;
use function Latitude\QueryBuilder\alias;

class Table
{
    /**
     * The name of the table
     */
    private string $name;

    /**
     * The columns of the table
     * @var string[]
     */
    private array $columns;

    /**
     * Table constructor.
     * @param string $name
     * @param array $columns
     */
    public function __construct(string $name, array $columns)
    {
        $this->name = $name;
        $this->columns = $columns;
    }

    /**
     * Returns all columns
     * @return string[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Returns the name of the table
     * @return string
     */
    public function from(): string
    {
        return $this->name;
    }

    /**
     * Returns the column name prefixed with the table name.
     * @param string $name
     * @return string
     */
    public function column(string $name): string
    {
        return $this->name . '.' . $name;
    }

    /**
     * Returns an array with aliases for all columns. For example: column id of table mails will be returned
     * as an alias 'mails_id' for 'mails.id'.
     * @return ExpressionInterface[]
     */
    public function alias(): array
    {
        $prefix = $this->name . '_';
        return array_map(function ($column) use ($prefix) {
            return alias($this->name . '.' . $column, $prefix . $column);
        }, $this->columns);
    }

    /**
     * Filters all fields from a row that belongs to this table.
     * @param object $row
     * @return object
     */
    public function filter(object $row): object
    {
        $prefix = $this->name . '_';
        $prefixLength = strlen($prefix);
        $obj = new stdClass();
        foreach (get_object_vars($row) as $key => $element) {
            if (strpos($key, $prefix) === 0) {
                $prop = substr($key, $prefixLength);
                $obj->$prop = $element;
            }
        }
        return $obj;
    }

    /**
     * Returns the name of the table.
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
}
