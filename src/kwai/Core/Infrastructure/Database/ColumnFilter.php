<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Database;

use stdClass;

/**
 * @deprecated
 * Class ColumnFilter
 */
class ColumnFilter
{
    /**
     * The string to remove from the column name before
     * assigning it to the object.
     */
    private string $prefix;

    /**
     * ColumnFilter constructor.
     *
     * @param string $prefix
     */
    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Filters all fields from a row that has the same prefix and returns an object
     * with properties. The name of the property is the name of the original property stripped
     * with the prefix.
     *
     * @param object $row
     * @return object
     */
    public function filter(object $row): object
    {
        $prefixLength = strlen($this->prefix);
        $obj = new stdClass();
        foreach (get_object_vars($row) as $key => $element) {
            if (strpos($key, $this->prefix) === 0) {
                $prop = substr($key, $prefixLength);
                $obj->$prop = $element;
            }
        }
        return $obj;
    }
}
