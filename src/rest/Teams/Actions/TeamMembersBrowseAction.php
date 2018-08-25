<?php
namespace REST\Teams\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamsTable;

class TeamMembersBrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        try {
            $team = TeamsTable::getTableFromRegistry()->get(
                $args['id'],
                [
                    'contain' => ['Members', 'Members.Person', 'Members.Person.Nationality']
                ]
            );

            //TODO: Remove sport dependency?
            return (new \Core\ResourceResponse(
                \Judo\Domain\Member\MemberTransformer::createForCollection($team->members)
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Team doesn't exist"));
        }
    }
}
