<?php
declare(strict_types=1);

use Kwai\Core\Domain\Permission;
use Kwai\Modules\Users\Domain\Rule;

it('can check a permission', function () {
    $rule = new Rule(
        name: 'Coach',
        subject: 'coach',
        permission: 15
    );

    expect($rule->hasPermission(Permission::CanUpdate))->toBeTrue();
    expect($rule->hasPermission(Permission::CanCreate))->toBeTrue();
});
