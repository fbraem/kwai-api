<?php

namespace Core;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use Aura\Payload\Payload;

interface ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface;
}
