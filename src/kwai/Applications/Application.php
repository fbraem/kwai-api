<?php
/**
 * @package Kwai
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Cake\Datasource\ConnectionManager;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\Dependency;
use Kwai\Core\Infrastructure\Dependencies\LoggerDependency;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Middlewares\JsonBodyParserMiddleware;
use Kwai\Core\Infrastructure\Middlewares\LogActionMiddleware;
use Kwai\Core\Infrastructure\Middlewares\ParametersMiddleware;
use Kwai\Core\Infrastructure\Middlewares\TokenMiddleware;
use Kwai\Core\Infrastructure\Middlewares\TransactionMiddleware;
use League\Container\Container;
use Psr\Http\Server\MiddlewareInterface;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Tuupola\Middleware\CorsMiddleware;

/**
 * Class Application
 *
 * Base class of all applications.
 */
abstract class Application
{
    private Container $container;

    private string $name;

    private string $path;

    private App $app;

    public const UUID_REGEX = '{uuid:[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}+}';

    /**
     * Application constructor.
     *
     * @param string|array $name
     * @param string|null  $basePath
     */
    public function __construct($name, ?string $basePath = '/api')
    {
        if (is_array($name)) {
            $this->name = (string) array_key_first($name);
            $this->path = $name[$this->name];
        } else {
            $this->name = $name;
            $this->path = "/$name";
        }
        $this->createContainer();

        //TODO: this is the old CAKEPHP connection ... keep it here until it isn't used anymore.
        if (ConnectionManager::getConfig('default') == null) {
            $dbConfig = $this->container->get('settings')['database'];
            $dbDefault = $this->container->get('settings')['default_database'];
            $dsnConfig = ConnectionManager::parseDsn($dbConfig[$dbDefault]['cake_dsn']);
            $dnsConfig['username'] = $dbConfig[$dbDefault]['user'];
            $dnsConfig['password'] = $dbConfig[$dbDefault]['pass'];
            ConnectionManager::setConfig('default', $dsnConfig);
        }

        $this->app = AppFactory::create();
        if (isset($basePath)) {
            $this->app->setBasePath($basePath);
        }

        $this->addMiddlewares();

        $me = $this;
        $this->app->group(
            $this->path,
            function (RouteCollectorProxy $group) use ($me) {
                $me->createRoutes($group);
            }
        );
    }

    /**
     * Create the DI container and add the dependencies
     */
    private function createContainer(): void
    {
        $this->container = new Container();
        $this->container->defaultToShared();
        AppFactory::setContainer($this->container);

        $this->container->add('settings', new Settings());
        $this->addDependencies();
    }

    /**
     * Add a dependency to the container
     *
     * @param string     $name
     * @param Dependency $dependency
     */
    protected function addDependency(string $name, Dependency $dependency): void
    {
        $this->container
            ->add($name, $dependency)
            ->addArgument($this->container->get('settings'))
        ;
    }

    /**
     * Add the core dependencies.
     */
    protected function addDependencies(): void
    {
        $this->addDependency('pdo_db', new DatabaseDependency());
        $this->addDependency('logger', new LoggerDependency());
    }

    /**
     * Add the middleware
     *
     * @param MiddlewareInterface $middleware
     */
    protected function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->app->addMiddleware($middleware);
    }

    /**
     * Add all required middlewares
     */
    protected function addMiddlewares(): void
    {
        $container = $this->container;

        $settings = $container->get('settings');
        if (isset($settings['cors'])) {
            $this->addMiddleware(new CorsMiddleware([
                'origin' => $settings['cors']['origin'] ?? '[*]',
                'methods' =>
                    $settings['cors']['method']
                        ?? ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
                'credentials' => true,
                'headers.allow' => ['Accept', 'Content-Type', 'Authorization'],
                'cache' => 0
            ]));
        }

        $this->addMiddleware(new ParametersMiddleware());
        $this->addMiddleware(new TransactionMiddleware($container));
        $this->addMiddleware(new LogActionMiddleware($container));
        $this->addMiddleware(new JsonBodyParserMiddleware());
        $this->addMiddleware(new TokenMiddleware($container));
    }

    /**
     * @param RouteCollectorProxy $group
     */
    abstract public function createRoutes(RouteCollectorProxy $group): void;

    /**
     * Run the application
     */
    public function run(): void
    {
        $app = $this->app;
        $app->addRoutingMiddleware();

        $errorMiddleware = $app->addErrorMiddleware(
            true,
            true,
            true,
            $this->container->get('logger')
        );

        $errorMiddleware->setErrorHandler(
            HttpNotFoundException::class,
            fn() => $app->getResponseFactory()->createResponse(404,'URL not found')
        );

        $app->run();
    }
}
