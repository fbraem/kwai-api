<?php
/**
 * This file contains macros that extend Collection
 */
declare(strict_types=1);

use Illuminate\Support\Collection;

/**
 * select will create a new collection by iterating over each element.
 * The callback receives item and key. The return value can be an array, a
 * value or null. When a value is returned, the original key will be used to
 * store the value in the new collection. When the callback returns an array,
 * the first element is used as key, the second element is the value. When null
 * is returned the item is discarded.
 *
 * One caveat: when the value is an array, you need to return
 * an array containing the original key and the original array as value.
 */
Collection::macro('select', function(callable $callback) {
    $newCollection = new Collection();
    $this->each(function($item, $key) use ($callback, $newCollection) {
        $result = $callback($item, $key);
        if (is_array($result)) {
            $newKey = array_key_first($result);
            if ($newKey === 0) {
                $newCollection->put($result[0], $result[1]);
            } else {
                $newCollection->put($newKey, $result[$newKey]);
            }
        } else {
            if ($result !== null) {
                $newCollection->put($key, $result);
            }
        }
    });
    return $newCollection;
});
