<?php

namespace Core\Responders;

class HTTPCodeResponder implements ResponderInterface
{
    private $responder;

    private $code;

    private $message;

    public function __construct(ResponderInterface $responder, $code, $message = "")
    {
        $this->responder = $responder;
        $this->code = $code;
        $this->message = $message;
    }

    public function respond()
    {
        $response = $this->responder->respond();
        return $response->withStatus($this->code, $this->message);
    }
}
