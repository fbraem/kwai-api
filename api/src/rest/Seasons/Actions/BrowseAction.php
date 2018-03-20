<?php

namespace REST\Seasons\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

class BrowseAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $seasons = \Domain\Game\SeasonsTable::getTableFromRegistry()
            ->find()
            ->order(['start_date' => 'DESC'])
            ->all();

        $payload->setOutput(\Domain\Game\SeasonTransformer::createForCollection($seasons));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
