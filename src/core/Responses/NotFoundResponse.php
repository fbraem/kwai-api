<?php

namespace Kwai\Core\Infrastructure\Responses;

use Psr\Http\Message\ResponseInterface as Response;

class NotFoundResponse
{
    private $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function __invoke(Response $response) : Response
    {
        return $response
            ->withStatus(404, $this->status);
        ;
    }
}
