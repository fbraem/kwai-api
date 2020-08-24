<?php

namespace REST\Trainings\Actions;

use Core\Validators\EntityExistValidator;
use Core\Validators\InputValidator;
use Core\Validators\ValidationException;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\CoachesTable;
use Domain\Training\CoachTransformer;

use Respect\Validation\Validator as v;

use Kwai\Core\Infrastructure\Presentation\Responses\UnprocessableEntityResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;

class CoachCreateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        try {
            (new InputValidator(
                [
                    'data.attributes.diploma' => [ v::length(1, 255), true ],
                    'data.attributes.active' => [ v::boolType(), true ],
                ]
            ))->validate($data);

            $coachesTable = CoachesTable::getTableFromRegistry();

            $member = (new EntityExistValidator('data.relationships.member', $coachesTable->Member, true))->validate($data);

            // Check if the member is not already a coach
            $coach = $coachesTable
                ->find()
                ->where(['member_id' => $member->id])
                ->first();
            if ($coach) {
                throw new ValidationException([
                    'data.relationships.member' => _('member is already a coach')
                ]);
            }

            $attributes = \JmesPath\search('data.attributes', $data);

            $coach = $coachesTable->newEntity();
            $coach->diploma = $attributes['diploma'];
            $coach->description = $attributes['description'];
            $coach->member = $member;
            $coach->active = $attributes['active'] ?? true;
            $coach->remark = $attributes['remark'];

            $coach->user = $request->getAttribute('clubman.user');

            $coachesTable->save($coach);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $coach->id);
            }

            $coachesTable->Member->loadInto($member, ['Person']);

            $response = (new ResourceResponse(
                CoachTransformer::createForItem($coach)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }

        return $response;
    }
}
