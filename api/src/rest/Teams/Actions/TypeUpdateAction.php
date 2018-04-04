<?php

namespace REST\Teams\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;
use Core\Responders\NotFoundResponder;

class TypeUpdateAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        $table = \Domain\Team\TeamTypesTable::getTableFromRegistry();
        try {
            $type = $table->get($id);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Team type doesn't exist.")
                ))->respond();
        }

        $data = $payload->getInput();

        $validator = new \REST\Teams\TeamTypeValidator();
        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return (
                new JSONErrorResponder(
                    new HTTPCodeResponder(
                        new Responder(),
                        422
                    ),
                    $errors
                )
            )->respond();
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        if (isset($attributes['name'])) {
            $type->name = $attributes['name'];
        }
        if (isset($attributes['start_age'])) {
            $type->start_age = $attributes['start_age'];
        }
        if (isset($attributes['end_age'])) {
            $type->end_age = $attributes['end_age'];
        }
        if (isset($attributes['competition'])) {
            $type->competition = $attributes['competition'];
        }
        if (isset($attributes['gender'])) {
            $type->gender = $attributes['gender'];
        }
        if (isset($attributes['active'])) {
            $type->active = $attributes['active'];
        }
        if (isset($attributes['remark'])) {
            $type->remark = $attributes['remark'];
        }

        $table->save($type);

        $payload->setOutput(\Domain\Team\TeamTypeTransformer::createForItem($type));

        return (
            new JSONResponder(
                new HTTPCodeResponder(
                    new Responder(),
                    201
                ),
                $payload
            ))->respond();
    }
}
