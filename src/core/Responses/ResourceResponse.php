<?php

namespace Core\Responses;

use Psr\Http\Message\ResponseInterface as Response;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Resource\ResourceInterface as ResourceInterface;

class ResourceResponse
{
    private $resource;

    private $includes;

    public function __construct(ResourceInterface $resource, $includes = '')
    {
        $this->resource = $resource;
        $this->includes = $includes;
    }

    public function __invoke(Response $response) : Response
    {
        $fractal = new Manager();
        if ($this->includes) {
            $fractal->parseIncludes($this->includes);
        }
        $fractal->setSerializer(new JsonApiSerializer());
        $data = $fractal->createData($this->resource)->toJson();

        $response->getBody()->write($data);

        return $response->withHeader('content-type', 'application/vnd.api+json');
    }

    public static function respond($resource, $response)
    {
        return (new self($resource))($response);
    }
}
