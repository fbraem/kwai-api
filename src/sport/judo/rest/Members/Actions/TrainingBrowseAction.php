<?php

namespace Judo\REST\Members\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use Judo\Domain\Member\MembersTable;
use Judo\Domain\Member\TrainingParticipationsTransformer;

use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class TrainingBrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');

        $table = MembersTable::getTableFromRegistry();

        try {
            $member = $table->get($args['id'], [
                'contain' => [ 'Trainings', 'Trainings.Event' ]
            ]);

            $response = (new ResourceResponse(
                TrainingParticipationsTransformer::createForCollection(
                    $member->trainings
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Member doesn't exist")))($response);
        }
        return $response;
    }
}
