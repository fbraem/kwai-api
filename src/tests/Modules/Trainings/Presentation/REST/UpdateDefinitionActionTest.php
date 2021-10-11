<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Modules\Trainings\Presentation\REST\UpdateDefinitionAction;
use Kwai\Modules\Users\Domain\User;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

it('execute UpdateDefinitionAction', function (array $data, string $id, Entity $user) {
    $action = new UpdateDefinitionAction();

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
})->with([
    [
        [
            'data' => [
                'type' => 'definitions',
                'id' => '10',
                'attributes' => [
                    'name' => 'Test',
                    'description' => 'Created while running unit test',
                    'weekday' => 1,
                    'start_time' => '19:00',
                    'end_time' => '21:00',
                    'time_zone' => 'Europe/Brussels'
                ]
            ]
        ],
        '10',
        new Entity(
            1,
            new User(
                new UniqueId(),
                new EmailAddress('jigoro.kano@kwai.com'),
                new Name('Jigoro', 'Kano')
            )
        )
    ]
]);
