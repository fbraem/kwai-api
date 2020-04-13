<?php

namespace REST\Teams\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamsTable;

use Cake\Datasource\Exception\RecordNotFoundException;

//TODO: Remove sport dependency?
use Judo\Domain\Member\MembersTable;
use Judo\Domain\Member\MemberTransformer;

use Kwai\Core\Infrastructure\Responses\ResourceResponse;

class TeamMembersAddAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $teamsTable = \Domain\Team\TeamsTable::getTableFromRegistry();
        try {
            $team = $teamsTable->get($args['id'], [
                'contain' => ['Members', 'Members.Person']
            ]);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Team doesn't exist"));
        }

        $membersTable = MembersTable::getTableFromRegistry();
        $json = $request->getParsedBody();
        foreach ($json['data'] as $memberData) {
            try {
                $member = $membersTable->get(
                    $memberData['id'],
                    [
                        'contain' => ['Person']
                    ]
                );
                $team->members[] = $member;
            } catch (RecordNotFoundException $rnfe) {
                //Skip this member
            }
        }

        $team->setDirty('members', true);
        $teamsTable->save($team);

        return (new ResourceResponse(
            MemberTransformer::createForCollection($team->members)
        ))($response);
    }
}
