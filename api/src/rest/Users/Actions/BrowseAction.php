<?php

namespace REST\Users\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

use League\Fractal;

class BrowseAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $userRepo = new \Domain\User\UserRepository();
        $users = $userRepo->all();

        $payload->setOutput(new Fractal\Resource\Collection($users, new \Domain\User\UserTransformer, 'users'));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
