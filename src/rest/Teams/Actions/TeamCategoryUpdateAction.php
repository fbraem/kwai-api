<?php

namespace REST\Teams\Actions;

use Core\Validators\InputValidator;
use Core\Validators\ValidationException;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamCategoriesTable;
use Domain\Team\TeamCategoryTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Respect\Validation\Validator as v;

use REST\Teams\TeamCategoryValidator;

use Kwai\Core\Infrastructure\Presentation\Responses\UnprocessableEntityResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;

class TeamCategoryUpdateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $table = TeamCategoriesTable::getTableFromRegistry();
        try {
            $data = $request->getParsedBody();
            $category = $table->get($args['id']);

            (new InputValidator([
                'data.attributes.name' => v::notEmpty()->length(1, 255),
                'data.attributes.active' => v::boolType(),
                'data.attributes.start_age' => v::digit(),
                'data.attributes.end_age' => v::digit(),
                'data.attributes.gender' => v::digit()->between(0, 2, true),
                'data.attributes.competition' => v::boolType()
            ], true))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            if (isset($attributes['name'])) {
                $category->name = $attributes['name'];
            }
            if (isset($attributes['start_age'])) {
                $category->start_age = $attributes['start_age'];
            }
            if (isset($attributes['end_age'])) {
                $category->end_age = $attributes['end_age'];
            }
            if (isset($attributes['competition'])) {
                $category->competition = $attributes['competition'];
            }
            if (isset($attributes['gender'])) {
                $category->gender = $attributes['gender'];
            }
            if (isset($attributes['active'])) {
                $category->active = $attributes['active'];
            }
            if (isset($attributes['remark'])) {
                $category->remark = $attributes['remark'];
            }

            $categoryValidator = (new TeamCategoryValidator())->validate($category);

            $table->save($category);

            $response = (new ResourceResponse(
                    TeamCategoryTransformer::createForItem($category)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse($ve->getErrors()))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Teamtype doesn't exist")))($response);
        }

        return $response;
    }
}
