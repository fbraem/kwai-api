<?php

namespace Judo\REST\Grades\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Judo\Domain\Member\GradesTable;
use Judo\Domain\Member\GradeTransformer;

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
            GradeTransformer::createForCollection(
                GradesTable::getTableFromRegistry()->find()->all()
            )
        ))($response);
    }
}
