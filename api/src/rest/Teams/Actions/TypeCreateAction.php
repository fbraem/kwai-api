<?php

namespace REST\Teams\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;

class TypeCreateAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
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

        $typesTable = \Domain\Team\TeamTypesTable::getTableFromRegistry();
        $type = $typesTable->newEntity();
        $type->name = $attributes['name'];
        $type->start_age = $attributes['start_age'];
        $type->end_age = $attributes['end_age'];
        $type->competition = $attributes['competition'];
        $type->gender = $attributes['gender'];
        $type->active = $attributes['active'];
        $type->remark = $attributes['remark'];
        $typesTable->save($type);

        $payload->setOutput(\Domain\Team\TeamTypeTransformer::createForItem($type));

        return (
            new JSONResponder(
                new HTTPCodeResponder(
                    new Responder(),
                    201
                ),
                $payload
            )
        )->respond();
    }
}
