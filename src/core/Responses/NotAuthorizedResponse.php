<?php

namespace Core\Responses;

use Psr\Http\Message\ResponseInterface as Response;

class NotFoundResponse
{
    private $message;

    public function __construct($message = 'Not Authorized')
    {
        $this->message = $message;
    }

    public function __invoke(Response $response) : Response
    {
        return $response
            ->withStatus(401, $this->message);
        ;
    }
}
