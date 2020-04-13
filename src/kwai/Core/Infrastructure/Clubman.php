<?php

namespace Kwai\Core\Infrastructure;

use Kwai\Core\Infrastructure\Middlewares\JsonBodyParserMiddleware;
use Kwai\Core\Infrastructure\Middlewares\LogActionMiddleware;
use Kwai\Core\Infrastructure\Middlewares\ParametersMiddleware;
use Kwai\Core\Infrastructure\Middlewares\TransactionMiddleware;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Infrastructure\Database;
use Kwai\Core\Infrastructure\Template\PlatesEngine;
use Kwai\Modules\Mails\Infrastructure\Mailer\MailerServiceFactory;
use Kwai\Modules\Mails\Infrastructure\Mailer\SmtpMailerService;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use League\Container\Container;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Slim\Factory\AppFactory;
use Tuupola\Middleware\JwtAuthentication;

/**
 * Class Clubman
 */
class Clubman
{
    private static Clubman $application;

    public static function getApplication(string $basePath = '/api')
    {
        if (self::$application == null) {
            $container = new Container();
            $container->defaultToShared();
            AppFactory::setContainer($container);

            $container->add('settings', function ($c) {
                $config = include __DIR__ . '/../../api/config.php';
                $config['displayErrorDetails'] = true;
                $config['determineRouteBeforeAppMiddleware'] = true;
                $config['outputBuffering'] = 'append';
                $config['httpVersion'] = '1.1';
                $config['responseChunkSize'] = 4096;
                return $config;
            })->addArgument($container);

            $dbConfig = $container->get('settings')['database'];
            $dbDefault = $container->get('settings')['default_database'];
            $dsnConfig = \Cake\Datasource\ConnectionManager::parseDsn($dbConfig[$dbDefault]['cake_dsn']);
            $dnsConfig['username'] = $dbConfig[$dbDefault]['user'];
            $dnsConfig['password'] = $dbConfig[$dbDefault]['pass'];
            \Cake\Datasource\ConnectionManager::setConfig('default', $dsnConfig);

            $container->add('pdo_db', function ($c) {
                $dbConfig = $c->get('settings')['database'];
                $dbDefault = $c->get('settings')['default_database'];
                return new Database\Connection(
                    $dbConfig[$dbDefault]['dsn'],
                    $dbConfig[$dbDefault]['user'],
                    $dbConfig[$dbDefault]['pass']
                );
            })->addArgument($container);

            $container->add('filesystem', function ($c) {
                $settings = $c->get('settings');
                $flyAdapter = new Local($settings['files']);
                return new Filesystem($flyAdapter);
            })->addArgument($container);

            $container->add('template', function ($c) {
                $settings = $c->get('settings');
                $variables = [
                    'website' => [
                        'url' => $settings['website']['url'],
                        'email' => $settings['website']['email'],
                    ]
                ];
                return new PlatesEngine(__DIR__ . '/../templates', $variables);
            })->addArgument($container);

            $container->add('mailer', function ($c) {
                $config = $c->get('settings');
                return (new MailerServiceFactory())->create(
                    $config['mail']['url']
                );
            })->addArgument($container);

            self::$application = AppFactory::create();
            self::$application->setBasePath($basePath);
            self::$application->add(new ParametersMiddleware());
            self::$application->add(new TransactionMiddleware($container));
            self::$application->add(new LogActionMiddleware($container));
            self::$application->add(new JsonBodyParserMiddleware());

            $settings = $container->get('settings');
            self::$application->add(new JwtAuthentication([
                'secret' => $settings['security']['secret'],
                'algorithm' => [ $settings['security']['algorithm'] ],
                'rules' => [ new AuthenticationRule() ],
                'error' => function ($response, $arguments) {
                    $data['status'] = 'error';
                    $data['message'] = $arguments['message'];
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                },
                'before' => function ($request, $arguments) use ($container) {
                    $uuid = new UniqueId($arguments['decoded']['sub']);
                    $userRepo = new UserDatabaseRepository($container->get('pdo_db'));
                    return $request->withAttribute(
                        'kwai.user',
                        $userRepo->getByUUID($uuid)
                    );
                }
            ]));

            // self::$application->add(new AuthenticationMiddleware($container));
            self::$application->addRoutingMiddleware();
        }
        return self::$application;
    }
}
