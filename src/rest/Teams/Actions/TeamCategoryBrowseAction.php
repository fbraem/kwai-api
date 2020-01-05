<?php

namespace REST\Teams\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamCategoriesTable;
use Domain\Team\TeamCategoryTransformer;

use Core\Responses\ResourceResponse;

class TeamCategoryBrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        return (new ResourceResponse(
                TeamCategoryTransformer::createForCollection(
                    TeamCategoriesTable::getTableFromRegistry()
                        ->find()
                        ->all()
                )
            ))($response);
    }
}
