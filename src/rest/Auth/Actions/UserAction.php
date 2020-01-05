<?php

namespace REST\Auth\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Core\Responses\ResourceResponse;

use Domain\User\UserTransformer;

class UserAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        return (new ResourceResponse(
            UserTransformer::createForItem(
                $request->getAttribute('clubman.user')
            )
        ))($response);
    }
}
