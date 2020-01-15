<?php
/**
 * Table class
 * @package Kwai
 * @subpackage Core
 * @author Franky Braem
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure;

/**
 * A class that represents a database table
 */
class DefaultTable implements Table
{
    /**
     * Table name
     * @var string
     */
    private $name;

    private $columns;

    /**
     * Constructor
     * @param string $name The name of the table
     */
    public function __construct(string $name, array $columns)
    {
        $this->name = $name;
        $this->columns = $columns;
    }

    public function columns(): array
    {
        return $this->columns;
    }

    /**
     * Returns the name of the table
     * @return string The table name
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Returns an associative array with column name as key
     * and a prefixed alias.
     * For example: Table 'users' with column 'id' will return
     * this array:
     *    [ 'users.id' => 'users_id' ]
     * @return array
     */
    public function alias(): array
    {
        $columns = array_map(function ($value) {
            return $this->name . '.' . $value;
        }, $this->columns());
        $aliases = array_map(function ($value) {
            return $this->prefix() . $value;
        }, $this->columns());
        return array_combine($columns, $aliases);
    }

    /**
     * Returns the prefix used to alias the column names. In this case
     * this is the tablename followed with _.
     * @return string
     */
    public function prefix(): string
    {
        return $this->name . '_';
    }

    /**
     * Returns the value for from in SELECT
     * @return string
     */
    public function from(): string
    {
        return $this->name;
    }
}
