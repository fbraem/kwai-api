<?php

namespace Core\Middlewares;

use Zend\Authentication\AuthenticationService;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

use Psr\Http\Message\ServerRequestInterface;

use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\FigRequestCookies;

use Zend\Diactoros\Response;

use Core\Responders\HTTPCodeResponder;
use Core\Responders\Responder;

class AuthorityMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $config = $request->getAttribute('clubman.config');
        
        // Create the Authentication service and storage
        $auth = new AuthenticationService();

        $authStorage = null;
        $authHeader = $request->getHeader('authorization');
        if ($authHeader) {
            list($jwt) = sscanf($authHeader[0], 'Bearer %s');
            $authStorage = new \Core\JWTStorage($config->jwt['secret'] , $jwt);
        } else {
            $authStorage = new \Core\JWTStorage($config->jwt['secret']);
        }
        $auth->setStorage($authStorage);
        $request = $request->withAttribute('clubman.authenticationService', $auth);

        // Get the XSFR cookie
        $xsfrCookie = FigRequestCookies::get($request, 'XSRF-TOKEN');

        // Check the JWT, if there is one ...
        $authStorage->read();
        $jwt = null;
        if ($auth->hasIdentity()) {
            $jwt = $authStorage->getToken();
            if ($jwt) {
                $request = $request->withAttribute('clubman.user', $jwt->getClaim('data')->id);
            }
        }

        // Check if we need to check the authorization
        $route = $request->getAttribute('clubman.route');
        if ($route->auth && $route->auth['login']) {
            // JWT can be empty when there is no JWT send
            if ($jwt === null) {
                return $this->createUnauthorizedResponse();
            }

            // When the JWT has a XSFR, check that the XSFR cookie contains
            // the same value.
            $xsfr = $jwt->getClaim('xsfr');
            if ($xsfr) {
                if ($xsfrCookie == null || $xsfrCookie->getValue() != $xsfr) {
                    return $this->createUnauthorizedResponse();
                }
            }

            if (isset($route->auth['permission'])) {
                //TODO: check permission.
            }
        }

        $response = $delegate->process($request);

        $xsfr = null;
        if ($authStorage->isEmpty()) {
            if ($xsfrCookie !== null) {
                 $xsfr = $xsfrCookie->getValue();
            }
        } else {
            $jwt = $authStorage->getToken();
            $xsfr = $jwt->getClaim('xsfr');
        }

        if ($xsfr !== null) {
            return FigResponseCookies::set(
                $response,
                SetCookie::create('XSRF-TOKEN')
                    ->withValue((string) $xsfr)
                    ->withPath('/')
                    //->withSecure(true) //TODO: Only possible when we use HTTPS
                    ->withHttpOnly(true)
            );
        }
        return $response;
    }

    private function createUnauthorizedResponse()
    {
        return (new HTTPCodeResponder(new Responder(), 401, _('You need to login to perform this action!')))->respond();
    }
}
