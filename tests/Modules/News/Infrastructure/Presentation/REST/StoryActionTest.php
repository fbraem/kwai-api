<?php

declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\News\Presentation\REST\CreateStoryAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Tests\Context;

$context = Context::createContext();

$now = (string) Timestamp::createNow();
$data = <<<JSON
{
    "data": {
        "type": "stories",
        "attributes": {
            "remark": "Created with StoryActionTest",
            "timezone": "Europe/Brussels",
            "publish_date": "${now}",
            "contents": [
                {
                    "title": "StoryActionTest",
                    "summary": "This story was created with StoryActionTest"
                }
            ]
        },
        "relationships": {
            "application": {
                "data": {
                    "type": "applications",
                    "id": "1"
                }
            }
        }
    }
}
JSON;

it('can create a story', function () use ($context, $data) {
    $action = new CreateStoryAction($context->db);

    $request = new ServerRequest(
        'POST',
        '/news/stories'
    );
    $request = $request
        ->withParsedBody(json_decode($data, true))
        ->withAttribute('kwai.user', $context->user)
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
