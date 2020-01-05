<?php

namespace REST\Trainings\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\Training\CoachesTable;
use Domain\Training\CoachTransformer;

use Core\Responses\NotFoundResponse;
use Core\Responses\OkResponse;

class CoachDeleteAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $coachesTable = CoachesTable::getTableFromRegistry();
        try {
            $coach = $coachesTable->get($args['id']);
            $coachesTable->delete($coach);
            $response = (new OKResponse())($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Coach doesn't exist")))($response);
        }
        return $response;
    }
}
