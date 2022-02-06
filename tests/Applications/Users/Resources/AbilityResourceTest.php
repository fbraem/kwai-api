<?php
declare(strict_types=1);

use Kwai\Applications\Users\Resources\RoleResource;
use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Role;
use Kwai\Modules\Users\Domain\Rule;

it('can serialize an Role to a JSON:API resource', function () {
    $role = new Entity(
        1,
        new Role(
            name: 'Coach',
            remark: 'All rules for a coach',
            rules: collect([
                new Entity(
                    1,
                    new Rule(
                        name: 'Manage Team',
                        subject: 'Team'
                    )
                )
            ])
        )
    );

    $resource = new RoleResource($role);

    try {
        $jsonapi = JSONAPI\Document::createFromObject($resource)->serialize();
    } catch (JSONAPI\Exception $e) {
        $this->fail((string) $e);
    }

    $json = json_decode($jsonapi);

    expect($json)
        ->toHaveProperty('data')
    ;
    expect($json->data)
        ->toMatchObject([
            'type' => 'roles',
            'id' => '1'
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'name' => 'Coach',
            'remark' => 'All rules for a coach'
        ])
    ;
    expect($json)
        ->toHaveProperty('included')
    ;
});
