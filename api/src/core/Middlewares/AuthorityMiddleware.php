<?php

namespace Core\Middlewares;

use Zend\Authentication\AuthenticationService;

use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

use Core\Responders\Responder;

use League\OAuth2\Server\Exception\OAuthServerException;

class AuthorityMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate)
    {
        // Check if we need to check the authorization
        $route = $request->getAttribute('clubman.route');
        if ($route->auth && $route->auth['login']) {
            $server = $request->getAttribute('clubman.container')['resourceServer'];
            try {
                $request = $server->validateAuthenticatedRequest($request);
                $request = $request->withAttribute('clubman.user', $request->getAttribute('oauth_user_id'));
            } catch(OAuthServerException $exception) {
                $response = (new Responder())->respond();
                return $exception->generateHttpResponse($response);
            } catch(\Exception $exception) {
                $response = (new Responder())->respond();
                return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                    ->generateHttpResponse($response);
            }

            if (isset($route->auth['permission'])) {
                //TODO: check permission.
            }
        }

        return $delegate->process($request);
    }
}
