<?php

namespace REST\Teams\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamsTable;
use Domain\Team\TeamTransformer;

use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;

use Cake\Datasource\Exception\RecordNotFoundException;

class TeamReadAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');
        $contain = [
            'Season',
            'TeamCategory'
        ];
        if (isset($parameters['include'])) {
            foreach ($parameters['include'] as $include) {
                if ($include == 'members') {
                    $contain[] = 'Members';
                    $contain[] = 'Members.Person';
                }
            }
        }

        try {
            $team = TeamsTable::getTableFromRegistry()->get(
                $args['id'],
                [
                    'contain' => $contain
                ]
            );
            if (in_array('Members', $contain)) {
                $team['members_count'] = count($team->members);
            }

            $response = (new ResourceResponse(
                TeamTransformer::createForItem(
                    $team
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Team doesn't exist")))($response);
        }

        return $response;
    }
}
