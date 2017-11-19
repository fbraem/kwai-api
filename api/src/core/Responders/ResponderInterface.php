<?php

namespace Core\Responders;

use Psr\Http\Message\ResponseInterface;

interface ResponderInterface
{
    public function respond() : ResponseInterface;
}
