<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Permission;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Modules\Users\Domain\Role;
use Kwai\Modules\Users\Domain\Rule;
use Kwai\Modules\Users\Domain\User;

it('can find a permission', function () {
    $readerRule = new Entity(
        2,
        new Rule(
            name: 'Read Article',
            subject: 'article',
            permission: 1
        )
    );

    $user = new User(
        uuid: new UniqueId(),
        emailAddress: new EmailAddress('jigoro.kano@kwai.com'),
        username: new Name('Jigoro', 'Kano'),
        roles: new Collection([
            new Entity(
                1,
                new Role(
                    name: 'admin',
                    rules: new Collection([
                        new Entity(
                            1,
                            new Rule(
                                name: 'manage',
                                subject: 'all',
                                permission: 15
                            )
                        ),
                        new Entity(
                            7,
                            new Rule(
                                name: 'create/update',
                                subject: 'training',
                                permission: 6
                            )
                        )
                    ])
                )
            ),
            new Entity(
                2,
                new Role(
                    name: 'author',
                    rules: new Collection([
                        $readerRule
                    ])
                )
            ),
            new Entity(
                3,
                new Role(
                    name: 'reader',
                    rules: new Collection([
                        $readerRule
                    ])
                )
            ),
            new Entity(
                4,
                new Role(
                    name: 'inviter',
                    rules: new Collection([
                        new Entity(
                            3,
                            new Rule(
                                name: 'Manage Invitation',
                                subject: 'invitation',
                                permission: 3
                            )
                        ),
                        new Entity(
                            4,
                            new Rule(
                                name: 'View Trainings',
                                subject: 'training',
                                permission: 1
                            )
                        )
                    ])
                )
            ),
            new Entity(
                5,
                new Role(
                    name: 'coach',
                    rules: new Collection([
                        new Entity(
                            5,
                            new Rule(
                                name: 'Manage Coaches',
                                subject: 'coach',
                                permission: 15
                            )
                        )
                    ])
                )
            ),

        ])
    );

    $allowed = $user->hasPermission('training', Permission::CanUpdate);
    expect($allowed)->toBeTrue();
});
