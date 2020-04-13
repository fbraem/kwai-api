<?php

namespace Kwai\Core\Infrastructure\Responses;

use Psr\Http\Message\ResponseInterface as Response;

class OkResponse
{
    public function __invoke(Response $response) : Response
    {
        return $response
            ->withStatus(200);
        ;
    }
}
