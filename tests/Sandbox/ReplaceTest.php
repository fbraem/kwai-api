<?php
declare(strict_types=1);

it('can remove a prefix from a string', function () {
    $prefixes = [
       '/^get/', '/^is/', '/^has/'
    ];

    $result = preg_replace($prefixes, '', 'getName');
    expect($result)->toEqual('Name');
    $result = preg_replace($prefixes, '', 'isActive');
    expect($result)->toEqual('Active');
    $result = preg_replace($prefixes, '', 'hasMember');
    expect($result)->toEqual('Member');
    $result = preg_replace($prefixes, '', 'setName');
    expect($result)->toEqual('setName');
});

it('can convert camelCase to camel_case', function () {
    $value = 'getNameOfCoach';
    $result = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1_', $value));
    expect($result)->toEqual('get_name_of_coach');
});
