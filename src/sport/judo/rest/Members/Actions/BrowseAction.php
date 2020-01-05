<?php

namespace Judo\REST\Members\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Judo\Domain\Member\MembersTable;
use Judo\Domain\Member\MemberTransformer;

use Core\Responses\ResourceResponse;

class BrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');

        $membersTable = MembersTable::getTableFromRegistry();

        $query = $membersTable->find();
        $concat = $query->func()->concat([
            'Person.lastname' => 'identifier',
            ' ',
            'Person.firstname' => 'identifier'
        ]);

        $contain = [
            'Person',
            'Person.Contact',
            'Person.Contact.Country',
            'Person.Nationality'
        ];

        $query->contain($contain);

        if (isset($parameters['filter']['name'])) {
            $query->where(function ($exp) use ($concat, $parameters) {
                $orCond = $exp->or_(function ($or) use ($parameters) {
                    return $or->like('Person.firstname', '%' . $parameters['filter']['name'] . '%');
                });
                $orCond = $orCond->like($concat, '%' . $parameters['filter']['name'] . '%');
                return $orCond->like('Person.lastname', '%' . $parameters['filter']['name'] . '%');
            });
        }

        if (isset($parameters['filter']['active'])) {
            $query->where([
                $membersTable->getAlias() . '.active'
                    => $parameters['filter']['active'] === 'true'
            ]);
        }

        $count = $query->count();
        $limit = $parameters['page']['limit'] ?? 10;
        $offset = $parameters['page']['offset'] ?? 0;

        $resource = MemberTransformer::createForCollection(
            $query->order([
                    'Person.lastname' => 'ASC',
                    'Person.firstname' => 'ASC'])
                ->all()
        );
        $resource->setMeta([
            'limit' => intval($limit),
            'offset' => intval($offset),
            'count' => $count
        ]);

        return (new ResourceResponse($resource))($response);
    }
}
