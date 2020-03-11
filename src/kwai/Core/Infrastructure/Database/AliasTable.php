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

class AliasTable
{
    /**
     * The original table
     */
    private Table $table;

    /**
     * The alias of the table
     */
    private string $alias;

    /**
     * AliasTable constructor.
     * @param Table $table
     * @param string $alias
     */
    public function __construct(Table $table, string $alias)
    {
        $this->table = $table;
        $this->alias = $alias;
    }

    /**
     * Returns an expression to use in the FROM clause
     * @return ExpressionInterface
     */
    public function from(): ExpressionInterface
    {
        return alias($this->table->from(), $this->alias);
    }

    /**
     * @see Table.column
     * @param string $name
     * @return string
     */
    public function column(string $name): string
    {
        return $this->table->column($name);
    }

    /**
     * Returns an array with alias expressions.
     * For example: A column id of table mails which is aliased as invitations will return 'invitations_id' as alias
     * for 'mails.id'.
     * @return ExpressionInterface[]
     */
    public function alias(): array
    {
        $prefix = $this->alias . '_';
        return array_map(function ($column) use ($prefix) {
            return alias($this->name() . '.' . $column, $prefix . $column);
        }, $this->table->getColumns());
    }

    /**
     * Filters all fields from a row that belongs to this table.
     * @param object $row
     * @return object
     */
    public function filter(object $row): object
    {
        $prefix = $this->alias . '_';
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
     * @see Table.name
     * @return string
     */
    public function name(): string
    {
        return $this->table->name();
    }
}
