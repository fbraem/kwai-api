<?php

namespace Core;

use Psr\Http\Message\RequestInterface;
use Aura\Payload\Payload;

interface ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload);
}
