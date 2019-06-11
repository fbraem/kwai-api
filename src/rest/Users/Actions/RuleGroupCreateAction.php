<?php

namespace REST\Users\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use \Cake\ORM\Entity;

use Domain\User\RuleGroupsTable;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;

class RuleGroupCreateAction
{
    private $container;

    private $inputValidator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->inputValidator = new InputValidator([
            'data.attributes.rule_group.name' => v::length(1, 255)
        ]);
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $ruleGroupsTable = RuleGroupsTable::getTableFromRegistry();

        try {
            $rules = (new EntityExistValidator(
                'data.relationships.rules',
                $ruleGroupsTable->Rules,
                false
            ))->validate($data);

            $this->eventInputValidator->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            $ruleGroup = $ruleGroupsTable->newEntity();
            $ruleGroup->name = $attributes['rule_group']['name'];
            $ruleGroup->remark = $attributes['rule_group']['remark'];
            $ruleGroup->user = $request->getAttribute('clubman.user');
            $ruleGroup->rules = $rules;

            $ruleGroupsTable->save($ruleGroup);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $ruleGroup->id);
            }
            $response = (new ResourceResponse(
                RuleGroupTransformer::createForItem($ruleGroup)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }

        return $response;
    }
}
