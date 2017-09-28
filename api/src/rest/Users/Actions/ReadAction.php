<?php

namespace REST\Users\Actions;

use Psr\Http\Message\RequestInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

use League\Fractal;

class ReadAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload)
    {
        $userRepo = new \Domain\User\UserRepository();

        $route = $request->getAttribute('clubman.route');
        $id = $route->getAttribute('id');
        $user = $userRepo->find($id);

        $payload->setOutput(new Fractal\Resource\Item($user, new \Domain\User\UserTransformer, 'users'));

        return new JSONResponder(new Responder(), $payload);
    }
}
