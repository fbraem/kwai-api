<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Modules\Trainings\Presentation\REST\CreateDefinitionAction;
use Kwai\Modules\Trainings\Presentation\REST\UpdateDefinitionAction;
use Kwai\Modules\Users\Domain\User;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

$data = [
    'data' => [
        'type' => 'definitions',
        'attributes' => [
            'name' => 'Test',
            'description' => 'Created while running unit test',
            'weekday' => 1,
            'start_time' => '19:00',
            'end_time' => '21:00',
            'time_zone' => 'Europe/Brussels'
        ]
    ]
];
$user = new Entity(
    1,
    new User(
        new UniqueId(),
        new EmailAddress('jigoro.kano@kwai.com'),
        new Name('Jigoro', 'Kano')
    )
);

it('can create a definition', function () use ($context, $data, $user) {
    $action = new CreateDefinitionAction(database: $context->db);

    $request = new ServerRequest(
        'PATCH',
        '/training/definitions/',
        []
    );
    $request = $request
        ->withParsedBody($data)
        ->withAttribute(
            'kwai.user',
            $user
        )
    ;
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);

    $result = json_decode((string) $response->getBody(), true);
    return $result['data']['id'];
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can update a definition', function ($id) use ($context, $data, $user) {
    $action = new UpdateDefinitionAction(database: $context->db);

    $data['data']['id'] = $id;
    $data['data']['attributes']['description'] = 'Updated with test "can update a definition"';

    $request = new ServerRequest(
        'PATCH',
        '/training/definitions/',
        []
    );
    $request = $request
        ->withParsedBody($data)
        ->withAttribute(
            'kwai.user',
            $user
        )
    ;
    $response = new Response();

    $response = $action($request, $response, ['id' => $id]);
    expect($response->getStatusCode())->toBe(200);
})
    ->depends('it can create a definition')
    ->skip(!Context::hasDatabase(), 'No database available')
;
