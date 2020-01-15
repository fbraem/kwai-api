<?php
/**
 * Table interface
 * @package Kwai
 * @subpackage Core
 * @author Franky Braem
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure;

/**
 * Interface for a database table
 */
interface Table
{
    /**
     * Returns the name of the table.
     * @return string The name of the table
     */
    public function name(): string;

    /**
     * Returns the columns of the table.
     * @return array The columns of the table
     */
    public function columns(): array;

    /**
     * Returns the columns for including them in the SQL statement.
     * @return array [description]
     */
    public function alias(): array;

    /**
     * Returns the prefix used for naming the columns.
     * @return string The prefix used for the column names
     */
    public function prefix(): string;

    /**
     * Returns the name of the table or an array with alias.
     * @return string|array
     */
    public function from();
}
