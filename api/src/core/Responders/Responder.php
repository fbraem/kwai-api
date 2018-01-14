<?php

namespace Core\Responders;

use Aura\Payload\Payload;
use Zend\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;

class Responder implements ResponderInterface
{
    public function respond() : ResponseInterface
    {
        return new Response();
    }
}
