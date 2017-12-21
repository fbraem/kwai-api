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
use League\OAuth2\Server\Grant\RefreshTokenGrant;

use Domain\Auth\AccessTokenTable;
use Domain\Auth\ClientTable;
use Domain\Auth\RefreshTokenTable;
use Domain\Auth\ScopeTable;
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

        $this->container['db'] = function ($c) use ($config) {
            $dbConnection = $config->database->{$config->default_database};
            return new \Zend\Db\Adapter\Adapter([
                'driver' => 'Pdo_Mysql',
                'database' => $dbConnection->name,
                'username' => $dbConnection->user,
                'password' => $dbConnection->pass,
                'hostname' => $dbConnection->host,
                'charset' =>  $dbConnection->charset,
            ]);
        };

        $this->container['filesystem'] = function ($c) use ($config) {
            $flyAdapter = new Local($config->files);
            return new Filesystem($flyAdapter);
        };
        $this->container['authorizationServer'] = function ($c) use ($config) {
            $server = new AuthorizationServer(
                new ClientTable($c['db']),
                new AccessTokenTable($c['db']),
                new ScopeTable($c['db']),
                $config->oauth2->private_key,
                $config->oauth2->encryption_key
            );
            $refreshTokenRepo = new RefreshTokenTable($c['db']);

            $grant = new PasswordGrant(
                new UserRepository($c['db']),
                $refreshTokenRepo
            );
            $grant->setRefreshTokenTTL(new \DateInterval('P1M'));
            $server->enableGrantType($grant, new \DateInterval('PT1H'));

            $grant = new RefreshTokenGrant($refreshTokenRepo);
            $grant->setRefreshTokenTTL(new \DateInterval('P1M'));
            $server->enableGrantType($grant, new \DateInterval('PT1H'));

            return $server;
        };
        $this->container['resourceServer'] = function ($c) use ($config) {
            return new ResourceServer(new AccessTokenTable($c['db']), $config->oauth2->public_key);
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
