<?php

namespace Core\Responders;

class NotFoundResponder implements ResponderInterface
{
    private $responder;

    private $message;

    public function __construct(ResponderInterface $responder, $message = "")
    {
        $this->responder = $responder;
        $this->message = $message;
    }

    public function respond()
    {
        $response = $this->responder->respond();
        return $response->withStatus(404, $this->message);
    }
}
