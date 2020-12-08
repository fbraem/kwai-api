<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Database;

use Illuminate\Support\Collection;
use IteratorAggregate;

/**
 * Class ColumnCollection
 */
class ColumnCollection implements IteratorAggregate
{
    private Collection $collection;

    /**
     * ColumnCollection constructor.
     *
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function getIterator()
    {
        return $this->collection->getIterator();
    }

    /**
     * Create a collection of data based on the collection of prefixes.
     * For example:
     *   training_id, training_name, author_name
     * With prefixes training_ and _author will result in two collections:
     * The first collection will contain id and name of the training, while
     * the second collection will contain the name of the author.
     *
     * The prefixes are used in the alias of columns.
     *
     * @param Collection $prefixes
     * @return Collection
     */
    public function filter(Collection $prefixes): Collection
    {
        return $this->collection->keys()->reduce(
            function ($result, $column) use ($prefixes) {
                $prefixes->each(function ($prefix, $pos) use (&$result, $column) {
                    if (str_starts_with($column, $prefix)) {
                        $originalColumn = substr($column, strlen($prefix));

                        // When there is no collection yet for the given position,
                        // push a new one.
                        $result->when(!$result->has($pos), function ($collection) {
                            return $collection->push(new Collection());
                        });

                        // Put the value in the collection with the original
                        // column name when it is not null.
                        if ($this->collection[$column]) {
                            $result->get($pos)->put($originalColumn, $this->collection[$column]);
                        }

                        // We found the original column, skip the other filters
                        return false;
                    }
                    return true;
                });
                return $result;
            },
            new Collection()
        );
    }
}
