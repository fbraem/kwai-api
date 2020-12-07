<?php

declare(strict_types=1);

use Illuminate\Support\Collection;

it('can transform a collection', function () {
    $col = new Collection([
        'name' => 'Jigoro Kano',
        'grade' => '12de DAN',
        'country' => 'Japan'
    ]);
    $newCol = $col->transformWithKeys(fn($item, $key) => match($key) {
        'name' => ['NAME' => strtoupper($item)],
        'country' => true,
        default => false
    });

    expect($newCol->toArray())
        ->toMatchArray([
            'NAME' => 'JIGORO KANO',
            'country' => 'Japan'
        ])
    ;
 });
