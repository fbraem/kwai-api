<?php
/**
 * AliasTable class
 * @package Kwai
 * @subpackage Core
 * @author Franky Braem
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure;

/**
 * A class that represents a database table with an alias
 */
final class AliasTable implements Table
{
    /**
     * Table name
     * @var Table
     */
    private $table;

    /**
     * The alias of the table
     * @var string
     */
    private $alias;

    /**
     * Constructor
     * @param Table $table The aliased the table
     * @param string $alias The alias of the table
     */
    public function __construct(Table $table, string $alias)
    {
        $this->table = $table;
        $this->alias = $alias;
    }

    public function columns(): array
    {
        return $this->table->columns();
    }

    /**
     * Returns an associative array with column name as key
     * and alias as value.
     * For example: Table 'users' with alias 'u' and column 'id' will return
     * this array:
     *    [ 'users.id' => 'u_id' ]
     * @return array
     */
    public function alias(): array
    {
        $columns = array_map(function ($value) {
            return $this->alias . '.' . $value;
        }, $this->columns());
        $aliases = array_map(function ($value) {
            return $this->prefix() . $value;
        }, $this->columns());
        return array_combine($columns, $aliases);
    }

    /**
     * Returns the prefix used to alias the column names. In this case
     * this is the alias of the table followed with _.
     * @return string
     */
    public function prefix(): string
    {
        return $this->alias . '_';
    }

    /**
     * Returns the name of the table
     * @return string The name of the table
     */
    public function name(): string
    {
        return $this->table->name();
    }

    public function from(): array
    {
        return [ $this->table->name() => $this->alias ];
    }
}
