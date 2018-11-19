<?php

namespace REST\Pages\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;

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
        $pagesTable = PagesTable::getTableFromRegistry();
        try {
            $page = $pagesTable->get($args['id'], [
                'contain' => ['Contents', 'Category', 'Contents.User']
            ]);

            $data = $request->getParsedBody();

            (new InputValidator([
                'data.attributes.priority' => v::digit(),
                'data.attributes.enabled' => v::boolType()
            ], true))->validate($data);

            $category = (new EntityExistValidator(
                'data.relationships.category',
                $pagesTable->Category,
                false
            ))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            if (isset($category)) {
                $page->catgory = $category;
            }
            if (isset($attributes['priority'])) {
                $page->priority = $attributes['priority'];
            }
            if (isset($attributes['enabled'])) {
                $page->enabled = $attributes['enabled'];
            }
            if (isset($attributes['remark'])) {
                $page->remark = $attributes['remark'];
            }

            $pagesTable->save($page);

            $filesystem = $this->container->get('filesystem');

            $response = (new \Core\ResourceResponse(
                PageTransformer::createForItem($page, $filesystem)
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Page doesn't exist")))($response);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse($ve->getErrors()));
        }

        return $response;
    }
}
