<?php

namespace Kwai\Core\Infrastructure\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

use Domain\User\UsersTable;

use Cake\Datasource\Exception\RecordNotFoundException;

use League\OAuth2\Server\Exception\OAuthServerException;

class AuthenticationMiddleware implements MiddlewareInterface
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function process(
        Request $request,
        RequestHandler $handler
    ) : ResponseInterface {
        $route = $request->getAttribute('route');
        if (! empty($route) && $route->getArgument('auth', false)) {
            $authorizationHeader = $request->getHeader('authorization');
            if ($request->getHeader('authorization') == null || count($authorizationHeader) == 0 || empty($authorizationHeader[0])) {
                // Doesn't make sence to test the authorization, if there isn't one ...
                return (new Response())->withStatus(401);
            }

            $server = $this->container->get('resourceServer');
            try {
                $request = $server->validateAuthenticatedRequest($request);
                $userId = $request->getAttribute('oauth_user_id');
                try {
                    $user = UsersTable::getTableFromRegistry()
                        ->get($userId, [
                            'contain' => [
                                'Abilities',
                                'Abilities.Rules',
                                'Abilities.Rules.RuleAction',
                                'Abilities.Rules.RuleSubject'
                            ]
                        ]);
                } catch (RecordNotFoundException $rnfe) {
                    return (new Response())->withStatus(500, _('Unable to find user'));
                }
                $request = $request->withAttribute('clubman.user', $user);
            } catch (OAuthServerException $exception) {
                if ($route->getName() != 'auth.logout') {
                    return $exception->generateHttpResponse((new Response()));
                }
            } catch (\Exception $exception) {
                return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                    ->generateHttpResponse((new Response()));
            }

            if (isset($route->auth['permission'])) {
                //TODO: check permission.
            }
        }
        return $handler->handle($request);
    }
}
