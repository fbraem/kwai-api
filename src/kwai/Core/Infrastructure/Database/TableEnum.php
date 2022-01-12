<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Database;

use Closure;
use MyCLabs\Enum\Enum;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;

/**
 * Class TableEnum
 *
 * A base class for an enumeration of table names.
 */
class TableEnum extends Enum
{
    public function alias(string $column) {
        return alias($column, $this->getAlias($column));
    }

    public function field(string $column) {
        return field($this->getColumn($column));
    }

    /**
     * Creates a function that calls the alias method for a column.
     *
     * @return Closure
     */
    public function getAliasFn()
    {
        return fn(string $column) =>
            alias(
                $this->getColumn($column),
                $this->getAlias($column)
            )
        ;
    }

    /**
     * Returns the column name prefixed with the tablename, separated with a dot.
     *
     * @param string $column
     * @return string
     */
    public function getColumn(string $column): string
    {
        return $this->getValue() . '.' . $column;
    }

    /**
     * Returns the alias name of a column.
     *
     * @param string $column
     * @return string
     */
    public function getAlias(string $column): string
    {
        return $this->getAliasPrefix() . $column;
    }

    /**
     * Returns the prefix used to create an alias. The prefix is the name of the table
     * followed with the underscore character.
     *
     * @return string
     */
    public function getAliasPrefix(): string
    {
        return $this->getValue() . '_';
    }

    /**
     * Returns a column filter for this table.
     *
     * @return ColumnFilter
     */
    public function createColumnFilter(): ColumnFilter
    {
        return new ColumnFilter($this->getAliasPrefix());
    }

    /**
     * Shortcut for calling getColumn.
     *
     * @param $name
     * @return string
     */
    public function __get($name): string
    {
        return $this->getColumn($name);
    }
}
