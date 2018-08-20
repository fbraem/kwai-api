<?php

namespace REST\Auth\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use League\OAuth2\Server\Exception\OAuthServerException;

use Domain\User\UsersTable;

class AuthorizeAction
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
            $authRequest = $server->validateAuthorizationRequest($request);

            //$authRequest->setUser((new UsersTable($db))->whereId(1)->findOne());
            $authRequest->setAuthorizationApproved(true);
            return $server->completeAuthorizationRequest($authRequest, $response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            // Catch unexpected exceptions
            $body = $response->getBody();
            $body->write($exception->getMessage());
            return $response->withStatus(500)->withBody($body);
        }
    }
}
