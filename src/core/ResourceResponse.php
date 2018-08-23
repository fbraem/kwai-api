<?php

namespace Core;

use Psr\Http\Message\ResponseInterface as Response;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Resource\ResourceInterface as ResourceInterface;

class ResourceResponse
{
    private $resource;

    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    public function __invoke(Response $response) : Response
    {
        $fractal = new Manager();
        $fractal->setSerializer(new JsonApiSerializer());
        $data = $fractal->createData($this->resource)->toJson();

        $response->getBody()->write($data);

        return $response->withHeader('content-type', 'application/vnd.api+json');
    }
}
