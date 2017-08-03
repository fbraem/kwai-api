<?php

namespace Core\Responders;

class JSONErrorResponder implements ResponderInterface
{
    private $responder;

    private $errors;

    public function __construct(ResponderInterface $responder, $errors)
    {
        $this->responder = $responder;
        $this->errors = $errors;
    }

    public function respond()
    {
        $response = $this->responder->respond();
        $response = $response->withHeader('content-type', 'application/vnd.api+json');

        $data = json_encode($this->errors);
        $response->getBody()->write($data);

        return $response;
    }
}
