<?php

namespace Core;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

use League\Plates\Engine as TemplateEngine;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;

use League\Container\Container;

use Domain\Auth\AccessTokenRepository;
use Domain\Auth\ClientRepository;
use Domain\Auth\RefreshTokenRepository;
use Domain\Auth\ScopeRepository;
use Domain\Auth\UserRepository;

use PHPMailer\PHPMailer\PHPMailer;

use Core\Middlewares\ParametersMiddleware;
use Core\Middlewares\LogActionMiddleware;
use Core\Middlewares\AuthenticationMiddleware;
use Core\Middlewares\TransactionMiddleware;

use Tuupola\Middleware\JwtAuthentication;

use Slim\Factory\AppFactory;

use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Infrastructure\Database;

//TODO: Extract all code to services, etc, ...
class Clubman
{
    private static $application;

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
                return new Database(
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
                return new TemplateEngine(__DIR__ . '/../templates');
            })->addArgument($container);

            $container->add('authorizationServer', function ($c) {
                $config = $c->get('settings');
                $server = new AuthorizationServer(
                    new ClientRepository(),
                    new AccessTokenRepository(),
                    new ScopeRepository(),
                    $config['oauth2']['private_key'],
                    $config['oauth2']['encryption_key']
                );
                $refreshTokenRepo = new RefreshTokenRepository();

                $grant = new PasswordGrant(
                    new UserRepository(),
                    $refreshTokenRepo
                );
                $grant->setRefreshTokenTTL(new \DateInterval('P1M'));
                $server->enableGrantType($grant, new \DateInterval('PT1H'));

                $grant = new RefreshTokenGrant($refreshTokenRepo);
                $grant->setRefreshTokenTTL(new \DateInterval('P1M'));
                $server->enableGrantType($grant, new \DateInterval('PT1H'));

                $server->enableGrantType(new ImplicitGrant(new \DateInterval('PT1H')));

                return $server;
            })->addArgument($container);
            $container->add('resourceServer', function ($c) {
                $config = $c->get('settings');
                return new ResourceServer(new AccessTokenRepository(), $config['oauth2']['public_key']);
            })->addArgument($container);

            $container->add('mailer', function ($c) {
                $config = $c->get('settings');
                $mail = new PHPMailer(true);
                //Server settings
                //$mail->SMTPDebug = 2;
                $mail->isSMTP();
                $mail->Host = $config['mail']['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $config['mail']['user'];
                $mail->Password = $config['mail']['pass'];
                $mail->SMTPSecure = 'tls';
                $mail->Port = $config['mail']['port'];

                //Recipients
                $recipient = $config['mail']['from'];
                $recipientMail = array_keys($recipient)[0];
                $mail->setFrom($recipientMail, $recipient[$recipientMail]);
                $mail->addReplyTo($recipientMail, $recipient[$recipientMail]);

                return $mail;
            })->addArgument($container);

            self::$application = AppFactory::create();
            self::$application->setBasePath($basePath);
            self::$application->add(new ParametersMiddleware());
            self::$application->add(new TransactionMiddleware($container));
            self::$application->add(new LogActionMiddleware($container));

            $settings = $container->get('settings');
            self::$application->add(new JwtAuthentication([
                'secret' => $settings['oauth2']['client']['secret'],
                'algorithm' => [ 'HS256' ],
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
