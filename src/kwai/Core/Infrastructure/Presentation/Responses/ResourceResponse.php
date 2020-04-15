<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation\Responses;

use Psr\Http\Message\ResponseInterface as Response;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Resource\ResourceInterface as ResourceInterface;

/**
 * Class ResourceResponse
 *
 * Returns a response with the resource transformed to the JSONAPI format.
 * The content-type header will be set to 'application/vnd.api+json'.
 */
class ResourceResponse
{
    private ResourceInterface $resource;

    private string $includes;

    /**
     * ResourceResponse constructor.
     *
     * @param ResourceInterface $resource
     * @param string            $includes
     */
    public function __construct(ResourceInterface $resource, string $includes = '')
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
}
