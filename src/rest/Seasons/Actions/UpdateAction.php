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

use Cake\Datasource\Exception\RecordNotFoundException;

class UpdateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $seasonsTable = SeasonsTable::getTableFromRegistry();
        try {
            $season = $seasonsTable->get(
                $args['id'],
                ['contain' => ['Teams']]
            );
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Season doesn't exist"));
        }

        $data = $request->getParsedBody();

        $validator = new SeasonInputValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        if (array_key_exists('name', $attributes)) {
            $season->name = $attributes['name'];
        }
        if (array_key_exists('start_date', $attributes)) {
            $season->start_date = $attributes['start_date'];
        }
        if (array_key_exists('end_date', $attributes)) {
            $season->end_date = $attributes['end_date'];
        }
        if (array_key_exists('remark', $attributes)) {
            $season->remark = $attributes['remark'];
        }

        $validator = new SeasonValidator();
        if (! $validator->validate($season)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $seasonsTable->save($season);

        return (new \Core\ResourceResponse(
            SeasonTransformer::createForItem($season)
        ))($response);
    }
}
