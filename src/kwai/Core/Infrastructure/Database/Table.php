<?php
/**
 * @package Kwai
 * @subpackage Core
 * @author Franky Braem
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Database;

use function Latitude\QueryBuilder\alias;

class Table
{
    private $name;

    private $columns;

    public function __construct(string $name, array $columns)
    {
        $this->name = $name;
        $this->columns = $columns;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function from(): string
    {
        return $this->name;
    }

    public function alias(): array
    {
        $prefix = $this->name . '_';
        return array_map(function ($column) use ($prefix) {
            return alias($this->name . '.' . $column, $prefix . $column);
        }, $this->columns);
    }

    public function filter(object $row): object
    {
        $prefix = $this->name . '_';
        $prefixLength = strlen($prefix);
        $obj = new \stdClass();
        foreach (get_object_vars($row) as $key => $element) {
            if (strpos($key, $prefix) === 0) {
                $prop = substr($key, $prefixLength);
                $obj->$prop = $element;
            }
        }
        return $obj;
    }
}
