<?php
namespace Core;

use Zend\Diactoros\Response;
use Zend\Diactoros\Server;
use Zend\Stratigility\MiddlewarePipe;
use Zend\Stratigility\NoopFinalHandler;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

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

/**
 * The Application class.
 */
class Application
{
    /**
     * @var The whoops instance
     */
    private $whoops;

    /**
     * @var The configuration
     */
    private $config;

    /**
     * @var The dependency injection container
     */
    private $container;

    /**
     * The Application constructor.
     *
     * @param Zend\Config\Config $config The configuration
     */
    public function __construct($config)
    {
        $this->config = $config;

        $this->whoops = new \Whoops\Run();
        $this->whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        $this->whoops->register();

        $this->container = new \Pimple\Container();

        \Cake\Datasource\ConnectionManager::config('default', [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'host' => $config->database->{$config->default_database}->host,
            'username' => $config->database->{$config->default_database}->user,
            'password' => $config->database->{$config->default_database}->pass,
            'database' => $config->database->{$config->default_database}->name,
            'encoding' => $config->database->{$config->default_database}->charset
        ]);

        $this->container['filesystem'] = function ($c) use ($config) {
            $flyAdapter = new Local($config->files);
            return new Filesystem($flyAdapter);
        };
        $this->container['authorizationServer'] = function ($c) use ($config) {
            $server = new AuthorizationServer(
                new ClientRepository(),
                new AccessTokenRepository(),
                new ScopeRepository(),
                $config->oauth2->private_key,
                $config->oauth2->encryption_key
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
        $this->container['resourceServer'] = function ($c) use ($config) {
            return new ResourceServer(new AccessTokenRepository(), $config->oauth2->public_key);
        };
    }

    /**
     * Returns the configuration.
     * @return Zend\Config\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns the dependency injection container
     * @return Pimple\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Runs the application by ordering the server to listen for a request.
     */
    public function run()
    {
        $app = new MiddlewarePipe();
        $app->setResponsePrototype(new Response());

        $app->pipe(new Middlewares\SetupMiddleware($this));
        $app->pipe(new Middlewares\RoutingMiddleware());
        $app->pipe(new Middlewares\AuthorityMiddleware());
        $app->pipe(new Middlewares\ParametersMiddleware());
        $app->pipe(new Middlewares\LogActionMiddleware());
        $app->pipe(new Middlewares\ActionMiddleware());
        $server = Server::createServer(
            $app,
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
        $server->listen(new NoopFinalHandler());
    }
}
