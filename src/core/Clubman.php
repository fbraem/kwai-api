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

class Clubman
{
    private static $application;

    private static $slimApplication;

    public static function getApplication()
    {
        if (self::$application == null) {
            self::$application = new Application(new \Zend\Config\Config(include __DIR__ . '/../../api/config.php'));
        }
        return self::$application;
    }

    public static function getSlimApplication()
    {
        if (self::$slimApplication == null) {
            $config = include __DIR__ . '/../../api/config.php';
            $config['displayErrorDetails'] = true;
            $config['determineRouteBeforeAppMiddleware'] = true;
            $app = new \Slim\App([
                'settings' => $config
            ]);

            \Cake\Datasource\ConnectionManager::setConfig('default', [
                'className' => 'Cake\Database\Connection',
                'driver' => 'Cake\Database\Driver\Mysql',
                'host' => $config['database'][$config['default_database']]['host'],
                'username' => $config['database'][$config['default_database']]['user'],
                'password' => $config['database'][$config['default_database']]['pass'],
                'database' => $config['database'][$config['default_database']]['name'],
                'encoding' => $config['database'][$config['default_database']]['charset']
            ]);

            $app->getContainer()['filesystem'] = function ($c) {
                $settings = $c->get('settings');
                $flyAdapter = new Local($settings['files']);
                return new Filesystem($flyAdapter);
            };

            $app->getContainer()['template'] = function ($c) {
                return new League\Plates\Engine(__DIR__ . '../../templates');
            };

            $app->getContainer()['authorizationServer'] = function ($c) {
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
            $app->getContainer()['resourceServer'] = function ($c) {
                $config = $c->get('settings');
                return new ResourceServer(new AccessTokenRepository(), $config['oauth2']['public_key']);
            };

            self::$slimApplication = $app;
        }
        return self::$slimApplication;
    }
}
