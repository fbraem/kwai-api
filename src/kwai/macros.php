<?php
/**
 * This file contains macros that extend Collection
 */
declare(strict_types=1);

use Illuminate\Support\Collection;

/**
 * transformWithKeys will create a new collection by iterating over each element
 * and pass the value and key to the callback. The result of the callback will
 * define what will happen.
 * If the returned value is a boolean true, the original key and item will be
 * added. When this value is a boolean false, the key and item will be ignored.
 * When the returned value is an associated array, the first key and value
 * will be added to the collection.
 *
 * ```
 * $col = new Collection(
 *      'name' => 'Jigoro Kano',
 *      'grade' => '12de DAN'
 * );
 * $newCol = $col->transformWithKeys(fn($item, $key) => match($key) {
 *      'name' => ['NAME' => strtoupper($item)],
 *      default => false
 * });
 * ```
 *
 * This code will give the following result:
 * [ 'NAME' => 'JIGORO KANO' ]
 */
Collection::macro('transformWithKeys', function(callable $callback) {
    $newCollection = new Collection();
    $this->each(function($item, $key) use ($callback, $newCollection) {
        $result = $callback($item, $key);
        if (is_bool($result)) {
            if ($result) {
                $newCollection->put($key, $item);
            }
        }
        else if (is_array($result)) {
            $newKey = array_key_first($result);
            $newCollection->put($newKey, $result[$newKey]);
        }
    });
    return $newCollection;
});

Collection::macro('filterColumns', function(Collection $prefixes) {
    return $this->keys()->reduce(
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
                    $value = $this->get($column, null);
                    if ($value !== null) {
                        $result->get($pos)->put($originalColumn, $value);
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
});

Collection::macro('recursive', function () {
    return $this->map(function ($value) {
        return is_array($value)
            ? (new static($value))->recursive()
            : $value;
    });
});
