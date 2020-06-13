<?php
/**
 * @package Kwai
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Cake\Datasource\ConnectionManager;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\Dependency;
use Kwai\Core\Infrastructure\Dependencies\LoggerDependency;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Middlewares\JsonBodyParserMiddleware;
use Kwai\Core\Infrastructure\Middlewares\LogActionMiddleware;
use Kwai\Core\Infrastructure\Middlewares\ParametersMiddleware;
use Kwai\Core\Infrastructure\Middlewares\TransactionMiddleware;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use League\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Tuupola\Middleware\JwtAuthentication;
use Tuupola\Middleware\JwtAuthentication\RuleInterface;

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
        $this->app->setBasePath($basePath);

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

        $this->addMiddleware(new ParametersMiddleware());
        $this->addMiddleware(new TransactionMiddleware($container));
        $this->addMiddleware(new LogActionMiddleware($container));
        $this->addMiddleware(new JsonBodyParserMiddleware());

        $settings = $container->get('settings');
        $this->addMiddleware(new JwtAuthentication([
            'secret' => $settings['security']['secret'],
            'algorithm' => [$settings['security']['algorithm']],
            'rules' => [
                // When the route contains the argument auth with the value true,
                // this middleware must run!
                new class implements RuleInterface {
                    public function __invoke(ServerRequestInterface $request)
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
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->getBody()->write(
                        json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
                    );
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
        $this->app->addRoutingMiddleware();
        $this->app->run();
    }
}
