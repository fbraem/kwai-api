<?php

namespace Core\Responders;

use Aura\Payload\Payload;
use Zend\Diactoros\Response;

class Responder implements ResponderInterface
{
    public function respond()
    {
        return new Response();
    }
}
