<?php

namespace REST\Auth\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use League\OAuth2\Server\Exception\OAuthServerException;

use Core\Responders\Responder;

class AuthorizeAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $server = $request->getAttribute('clubman.container')['authorizationServer'];
        $response = (new Responder())->respond();
        try {
            $authRequest = $server->validateAuthorizationRequest($request);

            $db = $request->getAttribute('clubman.container')['db'];
            $authRequest->setUser((new \Domain\User\UsersTable($db))->whereId(1)->findOne());
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
