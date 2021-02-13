<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Middlewares;

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;
use Tuupola\Middleware\JwtAuthentication;
use Tuupola\Middleware\JwtAuthentication\RuleInterface;

/**
 * Class TokenMiddleware
 * A wrapper around JwtAuthentication middleware.
 */
class TokenMiddleware implements MiddlewareInterface
{
    private MiddlewareInterface $jwtMiddleware;

    public function __construct(ContainerInterface $container)
    {
        $settings = $container->get('settings');

        $this->jwtMiddleware = new JwtAuthentication([
            'secure' => true,
            'relaxed' => $settings['security']['relaxed'] ?? [],
            'secret' => $settings['security']['secret'],
            'algorithm' => [$settings['security']['algorithm']],
            'rules' => [
                // When the route contains the argument auth with the value true,
                // this middleware must run!
                new class implements RuleInterface {
                    public function __invoke(ServerRequestInterface $request): bool
                    {
                        $extra = $request->getAttribute('kwai.extra');
                        if (isset($extra)) {
                            return $extra['auth'] ?? false;
                        }
                        //TODO: Remove following code when Slim dependency is
                        // removed
                        $routeContext = RouteContext::fromRequest($request);
                        $route = $routeContext->getRoute();
                        return !empty($route) && $route->getArgument('auth', 'false') === 'true';
                    }
                }
            ],
            'error' => function (ResponseInterface $response, $arguments) use ($settings) {
                $data['status'] = 'error';
                $data['message'] = $arguments['message'];
                $response->getBody()->write(
                    json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
                );
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    // ->withHeader('Access-Control-Allow-Credentials', 'true')
                    // ->withHeader('Access-Control-Allow-Origin', $settings['cors']['origin'])
                ;
            },
            'before' => function (ServerRequestInterface $request, $arguments) use ($container) {
                $uuid = new UniqueId($arguments['decoded']['sub']);
                $userRepo = new UserDatabaseRepository($container->get('pdo_db'));
                try {
                    $user = $userRepo->getByUniqueId($uuid);
                    return $request
                        ->withAttribute('kwai.user', $user);
                } catch (UserNotFoundException) {
                }
                return null;
            }
        ]);
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->jwtMiddleware->process($request, $handler);
    }
}
