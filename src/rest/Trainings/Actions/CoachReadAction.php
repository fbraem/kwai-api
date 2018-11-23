<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\Training\CoachesTable;
use Domain\Training\CoachTransformer;

use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class CoachReadAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $table = CoachesTable::getTableFromRegistry();
        try {
            $coach = $table->get($args['id'], [
                'contain' => ['Member', 'Member.Person']
            ]);

            $response = (new ResourceResponse(
                CoachTransformer::createForItem(
                    $coach
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Coach doesn't exist")))($response);
        }
        return $response;
    }
}
