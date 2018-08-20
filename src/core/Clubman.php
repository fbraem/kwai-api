<?php

namespace Core;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

use League\Plates\Engine;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;

use Domain\Auth\AccessTokenRepository;
use Domain\Auth\ClientRepository;
use Domain\Auth\RefreshTokenRepository;
use Domain\Auth\ScopeRepository;
use Domain\Auth\UserRepository;

use Core\Middlewares\ParametersMiddleware;
use Core\Middlewares\LogActionMiddleware;
use Core\Middlewares\AuthenticationMiddleware;

class Clubman
{
    private static $application;

    public static function getApplication()
    {
        if (self::$application == null) {
            $container = new \Slim\Container();

            $container['settings'] = function ($c) {
                $config = include __DIR__ . '/../../api/config.php';
                $config['displayErrorDetails'] = true;
                $config['determineRouteBeforeAppMiddleware'] = true;
                $config['outputBuffering'] = 'append';
                $config['httpVersion'] = '1.1';
                $config['responseChunkSize'] = 4096;
                return $config;
            };
            $app = new \Slim\App($container);

            $dbConfig = $container->get('settings')['database'];
            $dbDefault = $container->get('settings')['default_database'];
            \Cake\Datasource\ConnectionManager::setConfig('default', [
                'className' => 'Cake\Database\Connection',
                'driver' => 'Cake\Database\Driver\Mysql',
                'host' => $dbConfig[$dbDefault]['host'],
                'username' => $dbConfig[$dbDefault]['user'],
                'password' => $dbConfig[$dbDefault]['pass'],
                'database' => $dbConfig[$dbDefault]['name'],
                'encoding' => $dbConfig[$dbDefault]['charset']
            ]);

            $container['filesystem'] = function ($c) {
                $settings = $c->get('settings');
                $flyAdapter = new Local($settings['files']);
                return new Filesystem($flyAdapter);
            };

            $container['template'] = function ($c) {
                return new League\Plates\Engine(__DIR__ . '../../templates');
            };

            $container['authorizationServer'] = function ($c) {
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
            };
            $container['resourceServer'] = function ($c) {
                $config = $c->get('settings');
                return new ResourceServer(new AccessTokenRepository(), $config['oauth2']['public_key']);
            };

            $app->add(new ParametersMiddleware());
            $app->add(new LogActionMiddleware($container));
            $app->add(new AuthenticationMiddleware($container));

            self::$application = $app;
        }
        return self::$application;
    }
}
