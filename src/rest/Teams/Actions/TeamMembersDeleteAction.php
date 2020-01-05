<?php

namespace REST\Teams\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamsTable;

use Cake\Datasource\Exception\RecordNotFoundException;

use Core\Responses\ResourceResponse;

//TODO: Remove sport dependency?
use Judo\Domain\Member\MembersTable;
use Judo\Domain\Member\MemberTransformer;

class TeamMembersDeleteAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $teamsTable = TeamsTable::getTableFromRegistry();
        try {
            $team = $teamsTable->get($args['id'], [
                'contain' => ['Members', 'Members.Person']
            ]);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Team doesn't exist"));
        }

        $membersTable = MembersTable::getTableFromRegistry();
        $json = $request->getParsedBody();
        $ids = [];

        foreach ($json['data'] as $memberData) {
            $ids[] = $memberData['id'];
        }
        $members = $membersTable->find()->where(['id IN' => $ids])->toList();
        if (count($members) > 0) {
            $teamsTable->Members->unlink($team, $members);
            $team->setDirty('members', true);
            $teamsTable->save($team);
            $team = TeamsTable::getTableFromRegistry()->get($args['id'], [
                'contain' => ['Members', 'Members.Person']
            ]);
        }

        return (new ResourceResponse(
            MemberTransformer::createForCollection($team->members)
        ))($response);
    }
}
