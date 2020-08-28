<?php

namespace REST\Users\Actions;

use Core\Validators\EntityExistValidator;
use Core\Validators\InputValidator;
use Core\Validators\ValidationException;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\ORM\Entity;
use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\User\AbilitiesTable;
use Domain\User\AbilityTransformer;

use Respect\Validation\Validator as v;

use Kwai\Core\Infrastructure\Presentation\Responses\UnprocessableEntityResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;

class AbilityUpdateAction
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
            $ability = $abilitiesTable->get(
                $args['id'],
                [
                    'contain' => [
                        'Rules',
                        'Rules.RuleAction',
                        'Rules.RuleSubject'
                    ]
                ]
            );

            $rules = (new EntityExistValidator(
                'data.relationships.rules',
                $abilitiesTable->Rules,
                false
            ))->validate($data);

            $this->inputValidator->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            if (isset($attributes['name'])) {
                $ability->name = $attributes['name'];
            }
            if (isset($attributes['remark'])) {
                $ability->remark = $attributes['remark'];
            }
            $ability->user = $request->getAttribute('clubman.user');
            $abilitiesTable->save($ability);

            if ($rules) {
                // When a rule is not passed to this function, it must be deleted
                $lookup = array_column($rules, null, 'id');
                $toDelete = [];
                foreach ($ability->rules as $rule) {
                    if (!$lookup[$rule->id]) {
                        $toDelete[] = $rule;
                    }
                }
                if (count($toDelete) > 0) {
                    $abilitiesTable->Rules->unlink($ability, $toDelete);
                }

                // When a rule is passed to this function and it's not in the
                // table, it must be insert
                $lookup = array_column($ability->rules, null, 'id');
                $toInsert = [];
                foreach ($rules as $rule) {
                    if (!$lookup[$rule->id]) {
                        $toInsert[] = $rule;
                    }
                }
                if (count($toInsert) > 0) {
                    $abilitiesTable->Rules->link($ability, $toInsert);
                }
            }

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $ability->id);
            }
            $response = (new ResourceResponse(
                AbilityTransformer::createForItem($ability)
            ))($response);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Ability doesn't exist.")))($response);
        }

        return $response;
    }
}
