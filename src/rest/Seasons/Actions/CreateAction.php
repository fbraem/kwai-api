<?php

namespace REST\Seasons\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Game\SeasonsTable;
use Domain\Game\SeasonTransformer;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use REST\Seasons\SeasonValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;

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

        try {
            (new InputValidator([
                'data.attributes.name' => v::notEmpty()->length(1, 255),
                'data.attributes.start_date' => v::notEmpty()->date('Y-m-d'),
                'data.attributes.end_date' => v::notEmpty()->date('Y-m-d')
            ]))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            $seasonsTable = SeasonsTable::getTableFromRegistry();
            $season = $seasonsTable->newEntity();
            $season->name = $attributes['name'];
            $season->start_date = $attributes['start_date'];
            $season->end_date = $attributes['end_date'];
            $season->remark = $attributes['remark'];

            $seasonValidator = new SeasonValidator();
            $seasonValidator->validate($season);

            $seasonsTable->save($season);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $season->id);
            }

            $response = (new ResourceResponse(
                SeasonTransformer::createForItem($season)
                ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }

        return $response;
    }
}
