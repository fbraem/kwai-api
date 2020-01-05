<?php

namespace REST\Teams\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamCategoriesTable;
use Domain\Team\TeamCategoryTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Core\Responses\NotFoundResponse;
use Core\Responses\ResourceResponse;

class TeamCategoryReadAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        try {
            $response = (new ResourceResponse(
                TeamCategoryTransformer::createForItem(
                    TeamCategoriesTable::getTableFromRegistry()->get($args['id'])
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Teamcategory doesn't exist")))($response);
        }
        return $response;
    }
}
