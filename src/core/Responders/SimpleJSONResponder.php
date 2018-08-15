<?php

namespace Core\Responders;

use Psr\Http\Message\ResponseInterface;

class SimpleJSONResponder implements ResponderInterface
{
    private $responder;

    private $data;

    public function __construct(ResponderInterface $responder, $data)
    {
        $this->responder = $responder;
        $this->data = $data;
    }

    public function respond() : ResponseInterface
    {
        $response = $this->responder->respond();
        $response = $response->withHeader('content-type', 'application/json');

        $response->getBody()->write(json_encode($this->data));

        return $response;
    }
}
