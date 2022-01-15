<?php

declare(strict_types=1);

it('can select new values', function() {
    $oldValues = collect([
        '1' => 'A',
        '3' => 'C'
    ]);
    $updatedValues = collect([
        '1' => 'A',
        '2' => 'B',
        '3' => 'C'
    ]);

    $newValues = $updatedValues->diffKeys($oldValues);
    expect($newValues->toArray())
        ->toMatchArray(['2' => 'B'])
    ;
});

it('can select removed values', function() {
    $oldValues = collect([
        '1' => 'A',
        '3' => 'C',
        '2' => 'B'
    ]);
    $updatedValues = collect([
        '1' => 'A',
        '3' => 'C'
    ]);

    $removedValues = $oldValues->diffKeys($updatedValues);
    expect($removedValues->toArray())
        ->toMatchArray(['2' => 'B'])
    ;
});

it('can select removed and new values', function() {
    $oldValues = collect([
        '1' => 'A',
        '3' => 'C'
    ]);
    $updatedValues = collect([
        '1' => 'A',
        '2' => 'B'
    ]);

    $newValues = $updatedValues->diffKeys($oldValues);
    expect($newValues->toArray())
        ->toMatchArray(['2' => 'B'])
    ;

    $removedValues = $oldValues->diffKeys($updatedValues);
    expect($removedValues->toArray())
        ->toMatchArray(['3' => 'C'])
    ;
});
