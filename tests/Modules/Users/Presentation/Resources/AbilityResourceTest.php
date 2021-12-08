<?php
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Domain\Ability;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Rule;
use Kwai\Modules\Users\Presentation\Resources\AbilityResource;

it('can serialize an Ability to a JSON:API resource', function () {
    $ability = new Entity(
        1,
        new Ability(
            name: 'Coach',
            remark: 'All rules for a coach',
            rules: collect([
                new Entity(
                    1,
                    new Rule(
                        name: 'Manage Team',
                        subject: 'Team',
                        action: 'manage'
                    )
                )
            ])
        )
    );

    $resource = new AbilityResource($ability);

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
            'type' => 'abilities',
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
