<?php
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Rule;
use Kwai\Modules\Users\Presentation\Resources\RuleResource;

it('can serialize a Rule entity to JSON:API', function () {
    $rule = new Entity(
        1,
        new Rule(
            name: 'AddUser',
            subject: 'User',
            action: 'Add'
        )
    );

    $resource = new RuleResource($rule);

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
            'type' => 'rules',
            'id' => '1'
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'name' => 'AddUser',
            'action' => 'Add',
            'subject' => 'User'
        ])
    ;
});