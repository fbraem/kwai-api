<?php

namespace REST\Seasons\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;
use Core\Responders\NotFoundResponder;

class UpdateAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        $seasonsTable = \Domain\Game\SeasonsTable::getTableFromRegistry();
        try {
            $season = $seasonsTable->get($id);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (new NotFoundResponder(new Responder(), _("Season doesn't exist.")))->respond();
        }

        $data = $payload->getInput();

        $validator = new \REST\Seasons\SeasonValidator();
        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return (new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), $errors))->respond();
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        if (array_key_exists('name', $attributes)) {
            $season->name = $attributes['name'];
        }
        if (array_key_exists('start_date', $attributes)) {
            $season->start_date = $attributes['start_date'];
        }
        if (array_key_exists('end_date', $attributes)) {
            $season->end_date = $attributes['end_date'];
        }
        if (array_key_exists('remark', $attributes)) {
            $season->remark = $attributes['remark'];
        }
        $seasonsTable->save($season);

        $payload->setOutput(\Domain\Game\SeasonTransformer::createForItem($season));
        return (new JSONResponder(new HTTPCodeResponder(new Responder(), 201), $payload))->respond();
    }
}
