<?php

namespace REST\Seasons\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;

use League\Fractal;

class CreateAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $data = $payload->getInput();

        $validator = new \REST\Seasons\SeasonValidator();
        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return (new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), $errors))->respond();
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $seasonsTable = \Domain\Game\SeasonsTable::getTableFromRegistry();
        $season = $seasonsTable->newEntity();
        $season->name = $attributes['name'];
        $season->start_date = $attributes['start_date'];
        $season->end_date = $attributes['end_date'];
        $season->remark = $attributes['remark'];
        $seasonsTable->save($season);

        $payload->setOutput(\Domain\Game\SeasonTransformer::createForItem($season));
        return (new JSONResponder(new HTTPCodeResponder(new Responder(), 201), $payload))->respond();
    }
}
