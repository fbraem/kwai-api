<?php

namespace REST\Seasons\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Game\SeasonsTable;
use Domain\Game\SeasonTransformer;
use REST\Seasons\SeasonValidator;
use REST\Seasons\SeasonInputValidator;
use REST\Seasons\SeasonEmptyValidator;

class CreateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $validator = new SeasonInputValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }
        $validator = new SeasonEmptyValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $seasonsTable = SeasonsTable::getTableFromRegistry();
        $season = $seasonsTable->newEntity();
        $season->name = $attributes['name'];
        $season->start_date = $attributes['start_date'];
        $season->end_date = $attributes['end_date'];
        $season->remark = $attributes['remark'];
        $seasonsTable->save($season);

        $validator = new SeasonValidator();
        if (! $validator->validate($season)) {
            return $validator->unprocessableEntityResponse($response);
        }

        return (new \Core\ResourceResponse(
            SeasonTransformer::createForItem($season)
        ))($response)->withStatus(201);
    }
}
