<?php

namespace Core\Responders;

use Aura\Payload\Payload;

use Psr\Http\Message\ResponseInterface;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

class JSONResponder implements ResponderInterface
{
    private $responder;

    private $payload;

    private $baseURL;

    public function __construct(ResponderInterface $responder, Payload $payload, $baseURL = '/api')
    {
        $this->responder = $responder;
        $this->payload = $payload;
        $this->baseURL = $baseURL;
    }

    public function respond() : ResponseInterface
    {
        $response = $this->responder->respond();
        $response = $response->withHeader('content-type', 'application/vnd.api+json');

        $resource = $this->payload->getOutput();
        $extras = $this->payload->getExtras();
        if (is_array($extras)) {
            $resource->setMeta($this->payload->getExtras());
        }

        $fractal = new Manager();
        $fractal->setSerializer(new JsonApiSerializer(/*$this->baseURL*/));
        $data = $fractal->createData($resource)->toJson();
        $response->getBody()->write($data);

        return $response;
    }
}
