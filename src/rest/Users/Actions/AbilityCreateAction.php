<?php

namespace REST\Users\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use \Cake\ORM\Entity;

use Domain\User\AbilitiesTable;
use Domain\User\AbilityTransformer;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;

class AbilityCreateAction
{
    private $container;

    private $inputValidator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->inputValidator = new InputValidator([
            'data.attributes.name' => v::length(1, 255)
        ]);
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $abilitiesTable = AbilitiesTable::getTableFromRegistry();

        try {
            $rules = (new EntityExistValidator(
                'data.relationships.rules',
                $abilitiesTable->Rules,
                false
            ))->validate($data);

            $this->inputValidator->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            $ability = $abilitiesTable->newEntity();
            $ability->name = $attributes['name'];
            $ability->remark = $attributes['remark'];
            $ability->user = $request->getAttribute('clubman.user');
            $ability->rules = $rules;

            $abilitiesTable->save($ability);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $ability->id);
            }
            $response = (new ResourceResponse(
                AbilityTransformer::createForItem($ability)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }

        return $response;
    }
}
