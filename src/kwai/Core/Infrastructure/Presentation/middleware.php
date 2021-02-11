<?php
/**
 * Returns a function that adds all middleware to the application.
 * The function needs one argument: the Slim application.
 *
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Middlewares\JsonBodyParserMiddleware;
use Kwai\Core\Infrastructure\Middlewares\LogActionMiddleware;
use Kwai\Core\Infrastructure\Middlewares\ParametersMiddleware;
use Kwai\Core\Infrastructure\Middlewares\TransactionMiddleware;
use Kwai\Core\Infrastructure\Presentation\AuthenticationRule;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;
use Tuupola\Middleware\CorsMiddleware;
use Tuupola\Middleware\JwtAuthentication;
use Tuupola\Middleware\JwtAuthentication\RuleInterface;

return function (App $application) {
    $container = $application->getContainer();

    $application->add(new ParametersMiddleware());
    $application->add(new TransactionMiddleware($container));
    $application->add(new LogActionMiddleware($container));
    $application->add(new JsonBodyParserMiddleware());

    $settings = $container->get('settings');
    $application->add(new JwtAuthentication([
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
                    $routeContext = RouteContext::fromRequest($request);
                    $route = $routeContext->getRoute();

                    return !empty($route) && $route->getArgument('auth', 'false') === 'true';
                }
            }
        ],
        'error' => function (ResponseInterface $response, $arguments) {
            $data['status'] = 'error';
            $data['message'] = $arguments['message'];
            $response
                ->getBody()
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT))
            ;
            return $response->withHeader('Content-Type', 'application/json');
        },
        'before' => function (ServerRequestInterface $request, $arguments) use ($container) {
            $uuid = new UniqueId($arguments['decoded']['sub']);
            $userRepo = new UserDatabaseRepository($container->get('pdo_db'));
            return $request->withAttribute(
                'kwai.user',
                $userRepo->getByUUID($uuid)
            );
        }
    ]));
    if (isset($settings['cors'])) {
        $application->addMiddleware(new CorsMiddleware([
            'origin' => $settings['cors']['origin'] ?? '*',
            'methods' =>
                $settings['cors']['method']
                ?? ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
            'credentials' => true,
            'headers.allow' => ['Accept', 'Accept-Language', 'Content-Type', 'Authorization'],
            'cache' => 0
        ]));
    }

    $application->addRoutingMiddleware();

    $logger = $container->get('logger');
    $errorMiddleware = $application->addErrorMiddleware(
        false,
        true,
        true,
        $logger
    );

    $errorMiddleware->setErrorHandler(
        HttpNotFoundException::class,
        fn() => $application->getResponseFactory()->createResponse(404,'URL not found')
    );

    $customErrorHandler = function (
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails,
        ?LoggerInterface $logger = null
    ) use ($application) {
        // Logger is always null, so get it from the container
        // see: https://github.com/slimphp/Slim/issues/2943
        $logger = $application->getContainer()->get('logger');
        if ($logger) {
            $logger->error(
                $exception->getFile() .
                '(' . $exception->getLine() . '): ' .
                $exception->getMessage()
            );
            $logger->info(
                $exception->getTraceAsString()
            );
        }

        $payload = ['error' => $exception->getMessage()];

        $response = $application->getResponseFactory()->createResponse(500, 'Unknown error occurred');
        $response->getBody()->write(
            json_encode($payload, JSON_UNESCAPED_UNICODE)
        );

        return $response;
    };
    $errorMiddleware->setDefaultErrorHandler($customErrorHandler);
};
