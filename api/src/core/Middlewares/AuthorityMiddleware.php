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

class AuthorityMiddleware implements MiddlewareInterface
{
    private $jwtConfig;

    public function __construct($jwtConfig)
    {
        $this->jwtConfig = $jwtConfig;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        // Create the Authentication service and storage
        $auth = new AuthenticationService();
        $authStorage = new \Core\JWTStorage($this->jwtConfig , $request);
        $auth->setStorage($authStorage);
        $request = $request->withAttribute('clubman.authenticationService', $auth);

        // Get the XSFR cookie
        $xsfrCookie = FigRequestCookies::get($request, 'XSRF-TOKEN');

        // Check the JWT, if there is one ...
        $jwt = null;
        try {
            $authStorage->read();
        } catch (\Firebase\JWT\ExpiredException $ee) {
            //return $this->createUnauthorizedResponse();
        }
        if ($auth->hasIdentity()) {
            $jwt = $authStorage->getJWTData();
            // Check the user in the jwt (if there is one)
            if ($jwt && $jwt->data && $jwt->data->id) {
                $userRepo = new \Domain\User\UserRepository();
                $user = $userRepo->find($jwt->data->id);
                //TODO: check active user?
                if ($user === null) {
                    return $this->createUnauthorizedResponse();
                }
                $request = $request->withAttribute('clubman.user', $user);
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
            if ($jwt->xsfr) {
                if ($xsfrCookie == null || $xsfrCookie->getValue() != $jwt->xsfr) {
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
            $xsfr = $authStorage->getXSFR();
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
