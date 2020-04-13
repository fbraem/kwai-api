<?php

namespace REST\Seasons\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Game\SeasonsTable;
use Domain\Game\SeasonTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Respect\Validation\Validator as v;

use Kwai\Core\Infrastructure\Validators\ValidationException;
use Kwai\Core\Infrastructure\Validators\InputValidator;
use REST\Seasons\SeasonValidator;

use Kwai\Core\Infrastructure\Responses\UnprocessableEntityResponse;
use Kwai\Core\Infrastructure\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Responses\NotFoundResponse;

use Carbon\Carbon;

class UpdateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        try {
            $seasonsTable = SeasonsTable::getTableFromRegistry();
            $season = $seasonsTable->get(
                $args['id'],
                ['contain' => ['Teams']]
            );

            (new InputValidator([
                'data.attributes.name' => v::notEmpty()->length(1, 255),
                'data.attributes.start_date' => v::notEmpty()->date('Y-m-d'),
                'data.attributes.end_date' => v::notEmpty()->date('Y-m-d')
            ], true))->validate($data);

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

            $seasonValidator = new seasonValidator();
            $seasonValidator->validate($season);

            $seasonsTable->save($season);

            $response = (new ResourceResponse(
                SeasonTransformer::createForItem($season)
            ))($response);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            return (new NotFoundResponse(_("Season doesn't exist")))($response);
        }

        return $response;
    }
}
