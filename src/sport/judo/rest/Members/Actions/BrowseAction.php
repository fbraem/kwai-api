<?php

namespace Judo\REST\Members\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Judo\Domain\Member\MembersTable;
use Judo\Domain\Member\MemberTransformer;

class BrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        return (new \Core\ResourceResponse(
            MemberTransformer::createForCollection(
                MembersTable::getTableFromRegistry()
                    ->find()
                    ->contain(['Person', 'Person.Contact', 'Person.Nationality'])
                    ->order([
                        'Person.lastname' => 'ASC',
                        'Person.firstname' => 'ASC'])
                    ->all()
            )
        ))($response);
    }
}
