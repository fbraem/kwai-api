<?php
/**
 * TableData class
 * @package Kwai
 * @subpackage Core
 * @author Franky Braem
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure;

/**
 * A class that helps to return prefixed column values from a table row.
 * For example:
 *    When a select returns a column with alias user_id, and you want to
 *    access the data without the prefix 'user_', use this class as follows:
 * <code>
 *    $data = new TableData($row, 'user_');
 *    $data->id
 * </code>
 */
final class TableData
{
    /**
     * The object with the data.
     * @var object
     */
    private $obj;

    /**
     * Constructor
     * @param object $data   A row object returned from a select
     * @param string $prefix The prefix of the column names
     */
    public function __construct(object $data, string $prefix = null)
    {
        if ($prefix) {
            $prefixLength = strlen($prefix);
            $this->obj = new \stdClass();
            foreach (get_object_vars($data) as $key => $element) {
                if (strpos($key, $prefix) === 0) {
                    $prop = substr($key, $prefixLength);
                    $this->obj->$prop = $element;
                }
            }
        } else {
            $this->obj = $data;
        }
    }

    public function __get(string $name)
    {
        if (isset($this->obj->$name)) {
            return $this->obj->$name;
        }
        return null;
    }
}
