<?php

namespace REST\Auth\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use League\OAuth2\Server\Exception\OAuthServerException;

class AccessTokenAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $server = $this->container->get('authorizationServer');
        try {
            $application = \Core\Clubman::getApplication();
            $request = $request->withAttribute('client_secret', $this->container->get('settings')['oauth2']['client']['secret']);
            return $server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        }
    }
}
