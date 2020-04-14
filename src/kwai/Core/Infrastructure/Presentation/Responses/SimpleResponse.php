<?php

namespace Kwai\Core\Infrastructure\Presentation\Responses;

use Psr\Http\Message\ResponseInterface as Response;

class SimpleResponse
{
    private $status;
    private $message;

    public function __construct($status, $message)
    {
        $this->status = $status;
        $this->message = $message;
    }

    public function __invoke(Response $response) : Response
    {
        return $response
            ->withStatus($this->status, $this->message)
        ;
    }
}
