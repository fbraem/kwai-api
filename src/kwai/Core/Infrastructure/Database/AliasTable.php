<?php
/**
 * @package Kwai
 * @subpackage Core
 * @author Franky Braem
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Database;

use function Latitude\QueryBuilder\alias;

class AliasTable
{
    private $table;

    private $alias;

    public function __construct(Table $table, string $alias)
    {
        $this->table = $table;
        $this->alias = $alias;
    }

    public function from(): string
    {
        return alias($this->table->from(), $this->alias);
    }

    public function alias(): array
    {
        $prefix = $this->alias . '_';
        return array_map(function ($column) use ($prefix) {
            return alias($this->table->name() . '.' . $column, $prefix . $column);
        }, $this->table->getColumns());
    }

    public function filter(object $row): object
    {
        $prefix = $this->alias . '_';
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
