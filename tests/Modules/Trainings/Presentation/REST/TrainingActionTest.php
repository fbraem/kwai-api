<?php

declare(strict_types=1);

use Kwai\Modules\Trainings\Presentation\REST\BrowseTrainingsAction;
use Kwai\Modules\Trainings\Presentation\REST\CreateTrainingAction;
use Kwai\Modules\Trainings\Presentation\REST\GetTrainingAction;
use Kwai\Modules\Trainings\Presentation\REST\UpdateTrainingAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

$data = <<<JSON
{
    "data": {
        "type": "trainings",
        "attributes": {
            "remark": "Created with unit test",
            "contents": [
                {
                    "title": "Middengroep",
                    "summary": "De middengroep traint elke woensdag van 19u tot 20u"
                }
            ],
            "event": {
                "location": "Sporthal Stekene",
                "start_date": "2021-10-20 17:00:00",
                "end_date": "2021-10-20 18:00:00",
                "timezone": "Europe/Brussels",
                "cancelled": false
            }
        },
        "relationships": {
            "definition": {
                "data": {
                    "type": "definitions",
                    "id": "1"
                }
            },
            "teams": {
                "data": [
                    {
                        "type": "teams",
                        "id": "2"
                    }
                ]
            },
            "coaches": {
                "data": [
                    {
                        "type": "coaches",
                        "id": "1"
                    }
                ]
            }    
        }
    }
}
JSON;

it('can create a training', function () use ($context, $data) {
    $action = new CreateTrainingAction(database: $context->db);

    $request = new ServerRequest(
        'POST',
        '/trainings'
    );

    $request = $request
        ->withParsedBody(json_decode($data, true))
        ->withAttribute(
            'kwai.user',
            $context->user
        )
    ;
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);

    $stream = $response->getBody();
    $stream->rewind();
    $json = json_decode($stream->getContents());
    return $json->data->id;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can update a training', function ($id) use ($context, $data) {
    $action = new UpdateTrainingAction(database: $context->db);

    $json = json_decode($data, true);
    $json['data']['id'] = $id;
    $json['data']['attributes']['remark'] = 'Updated with unit test';

    $request = new ServerRequest(
        'PATCH',
        '/trainings/' . $id
    );

    $request = $request
        ->withParsedBody($json)
        ->withAttribute(
            'kwai.user',
            $context->user
        )
    ;
    $response = new Response();

    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a training')
;

it('can get a training', function ($id) use ($context) {
    $action = new GetTrainingAction(database: $context->db);

    $request = new ServerRequest(
        'GET',
        '/trainings/' . $id
    );

    $response = new Response();
    $response = $action($request, $response, ['id' => $id]);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a training')
;

it('can browse trainings', function () use ($context) {
    $action = new BrowseTrainingsAction(database: $context->db);

    $request = new ServerRequest(
        'GET',
        '/trainings'
    );

    $response = new Response();
    $response = $action($request, $response, []);
    expect($response->getStatusCode())->toBe(200);
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
