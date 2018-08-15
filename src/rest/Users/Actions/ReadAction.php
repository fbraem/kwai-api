<?php

namespace REST\Users\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

class ReadAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');
        try {
            $user = \Domain\User\UsersTable::getTableFromRegistry()->get($id);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("User doesn't exist.")
                )
            )->respond();
        }

        $payload->setOutput(\Domain\User\UserTransformer::createForItem($user));
        return (
            new JSONResponder(
                new Responder(),
                $payload
            )
        )->respond();
    }
}
