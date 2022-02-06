<?php
/**
 * @package Applications
 * @subpackage Auth
 */
declare(strict_types = 1);

namespace Kwai\Applications\Auth\Actions;

use Kwai\Applications\Users\Resources\UserResource;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotAuthorizedResponse;
use Kwai\JSONAPI;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Routing\Annotation\Route;
use function depends;

/**
 * Class UserAction
 *
 * Action to get the logged-in user.
 */
#[Route(
    path: '/auth',
    name: 'auth.get',
    defaults: ['auth' => true],
    methods: ['GET']
)]
class GetUserAction extends Action
{
    public function __construct(
        private ?Connection $database = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $user = $request->getAttribute('kwai.user');
        if ($user) {
            $resource = new UserResource($user, $user);
            return (new JSONAPIResponse(
                JSONAPI\Document::createFromObject($resource)
            ))($response);
        }

        return (new NotAuthorizedResponse())($response);
    }
}
