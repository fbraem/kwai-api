<?php

declare(strict_types=1);

use Kwai\Modules\Trainings\Presentation\REST\CreateTrainingAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

$data = <<<JSON
{
    "data": {
        "type": "trainings",
        "attributes": {
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
                "time_zone": "Europe/Brussels",
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
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
