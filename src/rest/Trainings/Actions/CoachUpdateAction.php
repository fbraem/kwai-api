<?php

namespace REST\Trainings\Actions;

use Cake\Datasource\Exception\RecordNotFoundException;
use Core\Validators\InputValidator;
use Core\Validators\ValidationException;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\CoachesTable;
use Domain\Training\CoachTransformer;

use Respect\Validation\Validator as v;

use Kwai\Core\Infrastructure\Presentation\Responses\UnprocessableEntityResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;

class CoachUpdateAction
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
            $coachesTable = CoachesTable::getTableFromRegistry();
            $coach = $coachesTable->get($args['id'], [
                'contain' => ['Member', 'Member.Person']
            ]);

            (new InputValidator(
                [
                    'data.attributes.diploma' => [ v::length(1, 255), true ],
                    'data.attributes.active' => [ v::boolType(), true ],
                ]
            ))->validate($data);

            $coachesTable = CoachesTable::getTableFromRegistry();

            $attributes = \JmesPath\search('data.attributes', $data);

            if (isset($attributes['diploma'])) {
                $coach->diploma = $attributes['diploma'];
            }
            if (isset($attributes['description'])) {
                $coach->description = $attributes['description'];
            }
            if (isset($attributes['active'])) {
                $coach->active = $attributes['active'] ?? true;
            }
            if (isset($attributes['remark'])) {
                $coach->remark = $attributes['remark'];
            }

            $coach->user = $request->getAttribute('clubman.user');

            $coachesTable->save($coach);

            $response = (new ResourceResponse(
                CoachTransformer::createForItem($coach)
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Coach doesn't exist")))($response);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }

        return $response;
    }
}
