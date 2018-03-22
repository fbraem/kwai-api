<?php

namespace REST\Teams\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

class TeamBrowseAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $teams = \Domain\Team\TeamsTable::getTableFromRegistry()->find()->contain(['Season', 'TeamType'])->all();

        $payload->setOutput(\Domain\Team\TeamTransformer::createForCollection($teams));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
