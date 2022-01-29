<?php
declare(strict_types=1);

use Kwai\Modules\Users\Domain\Rule;

it('can check a permission', function () {
    $rule = new Rule(
        name: 'Coach',
        subject: 'coach',
        permission: 7
    );

    $edit = 1;
    expect($rule->hasPermission($edit))->toBeTrue();
    $create = 2;
    expect($rule->hasPermission($create))->toBeTrue();
});
