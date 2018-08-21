<?php

namespace Core;

use Psr\Http\Message\ResponseInterface as Response;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Resource\ResourceInterface as ResourceInterface;

abstract class Action
{
    public function createJSONResponse(Response $response, ResourceInterface $resource, $statusCode = 200) : Response
    {
        $fractal = new Manager();
        $fractal->setSerializer(new JsonApiSerializer(/*$this->baseURL*/));
        $data = $fractal->createData($resource)->toJson();

        $response->getBody()->write($data);

        return $response
            ->withStatus($statusCode)
            ->withHeader('content-type', 'application/vnd.api+json');
    }
}
