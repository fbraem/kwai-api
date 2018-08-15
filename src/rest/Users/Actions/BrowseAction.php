<?php

namespace REST\Users\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

class BrowseAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $users = \Domain\User\UsersTable::getTableFromRegistry()->find()->all();
        $payload->setOutput(\Domain\User\UserTransformer::createForCollection($users));
        return (
            new JSONResponder(
                new Responder(),
                $payload
            )
        )->respond();
    }
}
