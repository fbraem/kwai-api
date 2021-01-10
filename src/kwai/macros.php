<?php
/**
 * This file contains macros that extend Collection
 */
declare(strict_types=1);

use Illuminate\Support\Collection;

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

/**
 * Creates a collection. Nested arrays will also be changed to Collections
 */
Collection::macro('recursive', function () {
    return $this->map(function ($value) {
        return is_array($value)
            ? (new static($value))->recursive()
            : $value;
    });
});

/**
 * Gets an item collection for the given key. When the key doesn't exist
 * a new Collection will be created.
 */
Collection::macro('nest', function($key) {
    if (!$this->has($key)) {
        $this->put($key, new Collection());
    }
    return $this->get($key);
});
