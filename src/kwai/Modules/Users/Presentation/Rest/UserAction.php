<?php
declare(strict_types = 1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Core\Responses\NotFoundResponse;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Core\Responses\ResourceResponse;

use Kwai\Modules\Users\Presentation\Transformers\UserTransformer;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetCurrentUserCommand;
use Kwai\Modules\Users\UseCases\GetCurrentUser;

class UserAction
{
    /**
     * The DI container
     */
    private ContainerInterface $container;

    /**
     * UserAction constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $request->getAttribute('kwai.user');
        $command = new GetCurrentUserCommand([
            'uuid' => strval($user->getUuid())
        ]);

        try {
            $user = (new GetCurrentUser(
                new UserDatabaseRepository($this->container->get('pdo_db')),
                new AbilityDatabaseRepository($this->container->get('pdo_db'))
            ))($command);
        } catch (NotFoundException $e) {
            return (new NotFoundResponse('User not found'))($response);
        }

        return (new ResourceResponse(
            UserTransformer::createForItem(
                $user
            )
        ))($response);
    }
}
