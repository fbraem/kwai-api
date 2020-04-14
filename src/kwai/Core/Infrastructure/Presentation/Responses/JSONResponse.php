<?php

namespace Kwai\Core\Infrastructure\Presentation\Responses;

use Psr\Http\Message\ResponseInterface as Response;

class JSONResponse
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __invoke(Response $response) : Response
    {
        $response->getBody()->write(json_encode($this->data));
        return $response->withHeader('content-type', 'application/json');
    }
}
