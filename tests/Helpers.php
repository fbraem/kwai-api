<?php
declare(strict_types=1);

expect()->extend('tobeJSONAPIObject', function (string $type, int $pos = -1) {
    $this->toHaveKey('data')
        ->data
        ->toBeArray()
    ;

    expect($this->value["data"])
        ->toHaveKey('type')
        ->toHaveKey('id')
        ->toHaveKey('attributes')
    ;
    expect($this->value['data']['type'])
        ->toBe($type)
    ;
    expect($this->value['data']['attributes'])
        ->toBeArray()
    ;
});

expect()->extend('tobeJSONAPIArray', function (string $type, bool $haveMeta = false) {
    if ($haveMeta) {
        $this->toHaveKey('meta');
    }
    $this->toHaveKey('data')
        ->data
        ->toBeArray()
    ;

    expect($this->value["data"][0])
        ->toHaveKey('type')
        ->toHaveKey('id')
        ->toHaveKey('attributes')
    ;
    expect($this->value['data'][0]['type'])
        ->toBe($type)
    ;
    expect($this->value['data'][0]['attributes'])
        ->toBeArray()
    ;
});
