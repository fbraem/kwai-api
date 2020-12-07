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
