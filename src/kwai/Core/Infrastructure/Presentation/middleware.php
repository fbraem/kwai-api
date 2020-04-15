<?php
/**
 * Returns a function that adds all middleware to the application.
 * The function needs one argument: the Slim application.
 *
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Infrastructure\Middlewares\JsonBodyParserMiddleware;
use Kwai\Core\Infrastructure\Middlewares\LogActionMiddleware;
use Kwai\Core\Infrastructure\Middlewares\ParametersMiddleware;
use Kwai\Core\Infrastructure\Middlewares\TransactionMiddleware;
use Kwai\Core\Infrastructure\Presentation\AuthenticationRule;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Tuupola\Middleware\JwtAuthentication;

return function (App $application) {
    $container = $application->getContainer();

    $application->add(new ParametersMiddleware());
    $application->add(new TransactionMiddleware($container));
    $application->add(new LogActionMiddleware($container));
    $application->add(new JsonBodyParserMiddleware());

    $settings = $container->get('settings');
    $application->add(new JwtAuthentication([
        'secret' => $settings['security']['secret'],
        'algorithm' => [$settings['security']['algorithm']],
        'rules' => [
            new AuthenticationRule()
        ],
        'error' => function (ResponseInterface $response, $arguments) {
            $data['status'] = 'error';
            $data['message'] = $arguments['message'];
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
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

    $application->addRoutingMiddleware();
};
