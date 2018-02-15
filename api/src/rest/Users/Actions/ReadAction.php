<?php

namespace REST\Users\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

use League\Fractal;

class ReadAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $db = $request->getAttribute('clubman.container')['db'];
        $usersTable = new \Domain\User\UsersTable($db);

        $id = $request->getAttribute('route.id');
        try {
            $user = $usersTable->findOne();
        } catch (\Domain\NotFoundException $nfe) {
            return (new NotFoundResponder(new Responder(), _("User doesn't exist.")))->respond();
        }

        $payload->setOutput(new Fractal\Resource\Item($user, new \Domain\User\UserTransformer, 'users'));
        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
