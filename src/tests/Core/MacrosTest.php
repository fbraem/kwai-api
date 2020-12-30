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

it('can create a collection recursively', function () {
   $arr = [
       'a' => 1,
       'b' => [
           'c' => 3
       ]
   ];

   $collect = collect($arr)->recursive();
   expect($collect)
       ->toBeInstanceOf(Collection::class)
       ->and($collect->get('b'))
       ->toBeInstanceOf(Collection::class)
   ;
});

it('can filter columns', function () {
   $row = collect([
       'user_name' => 'Jigoro',
       'grade_name' => '12dan'
   ]);

   [$user, $grade] = $row->filterColumns(collect(['user_', 'grade_']));
   expect($user)
       ->toHaveKey('name')
       ->and($user->get('name'))
       ->toBe('Jigoro')
   ;
    expect($grade)
        ->toHaveKey('name')
        ->and($grade->get('name'))
        ->toBe('12dan')
    ;
});
