<?php
/**
 * @package Kwai
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Applications;

use Cake\Datasource\ConnectionManager;
use Exception;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Dependencies\Dependency;
use Kwai\Core\Infrastructure\Dependencies\LoggerDependency;
use Kwai\Core\Infrastructure\Dependencies\Settings;
use Kwai\Core\Infrastructure\Middlewares\CorsMiddleware;
use Kwai\Core\Infrastructure\Middlewares\ErrorMiddleware;
use Kwai\Core\Infrastructure\Middlewares\JsonBodyParserMiddleware;
use Kwai\Core\Infrastructure\Middlewares\LogActionMiddleware;
use Kwai\Core\Infrastructure\Middlewares\ParametersMiddleware;
use Kwai\Core\Infrastructure\Middlewares\RequestHandlerMiddleware;
use Kwai\Core\Infrastructure\Middlewares\RouteMiddleware;
use Kwai\Core\Infrastructure\Middlewares\TokenMiddleware;
use Kwai\Core\Infrastructure\Middlewares\TransactionMiddleware;
use Kwai\Core\Infrastructure\Presentation\Router;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Container\Container;
use Neomerx\Cors\Analyzer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Relay\Relay;

/**
 * Class KwaiApplication
 */
abstract class Application
{
    private ContainerInterface $container;

    private array $middlewareQueue = [];

    public const UUID_REGEX = '{uuid:[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}+}';

    /**
     * KwaiApplication Constructor
     */
    public function __construct()
    {
        $this->container = new Container();
        $this->container->defaultToShared();
        $this->container->add('settings', new Settings());

        $this->addDependencies();

        //TODO: this is the old CAKEPHP connection ... keep it here until it isn't used anymore.
        if (ConnectionManager::getConfig('default') == null) {
            $dbConfig = $this->container->get('settings')['database'];
            $dbDefault = $this->container->get('settings')['default_database'];
            $dsnConfig = ConnectionManager::parseDsn($dbConfig[$dbDefault]['cake_dsn']);
            $dnsConfig['username'] = $dbConfig[$dbDefault]['user'];
            $dnsConfig['password'] = $dbConfig[$dbDefault]['pass'];
            ConnectionManager::setConfig('default', $dsnConfig);
        }
    }

    abstract protected function createRouter(): Router;

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

    public function addMiddlewares(ContainerInterface $container): void
    {
    }

    /**
     * Add a middleware to the queue
     *
     * @param MiddlewareInterface $middleware
     */
    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->middlewareQueue[] = $middleware;
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        ob_start();
        $level = ob_get_level();

        try {
            $router = $this->createRouter();

            $psr17Factory = new Psr17Factory();
            $creator = new ServerRequestCreator(
                $psr17Factory,
                $psr17Factory,
                $psr17Factory,
                $psr17Factory
            );
            $serverRequest = $creator->fromGlobals();

            $settings = $this->container->get('settings');
            if (isset($settings['cors'])) {
                $corsSettings = new \Neomerx\Cors\Strategies\Settings();
                $scheme = $settings['cors']['server']['scheme'] ?? 'http';
                $corsSettings->init(
                    $scheme,
                    $settings['cors']['server']['host'],
                    $settings['cors']['server']['port'] ?? $scheme === 'http' ? 80 : 443
                );
                $corsSettings->enableCheckHost();
                $corsSettings->setAllowedOrigins($settings['cors']['origin'] ?? ['*']);
                $corsSettings->setAllowedMethods(
                    $settings['cors']['method'] ?? ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']
                );
                $corsSettings->setAllowedHeaders(['Accept', 'Accept-Language', 'Content-Type', 'Authorization']);
                $corsSettings->setCredentialsSupported();
                $cors = Analyzer::instance($corsSettings)->analyze($serverRequest);
            } else {
                $cors = null;
            }

            $this->addMiddleware(new ErrorMiddleware($psr17Factory, $psr17Factory, $cors));
            $this->addMiddleware(new RouteMiddleware($router, $psr17Factory));
            if ($cors) {
                $this->addMiddleware(new CorsMiddleware($psr17Factory, $cors));
            }
            $this->addMiddleware(new ParametersMiddleware());
            $this->addMiddleware(new TransactionMiddleware($this->container));
            $this->addMiddleware(new LogActionMiddleware($this->container));
            $this->addMiddleware(new JsonBodyParserMiddleware());
            $this->addMiddleware(new TokenMiddleware($this->container));

            $this->addMiddlewares($this->container);

            $this->addMiddleware(new RequestHandlerMiddleware(
                $this->container,
                $psr17Factory->createResponse()
            ));

            $relay = new Relay($this->middlewareQueue);
            $response = $relay->handle($serverRequest);

            $captured = '';
            while (ob_get_level() >= $level) {
                $captured = PHP_EOL . ob_get_clean() . $captured;
            }
            $body = $response->getBody();
            if ($captured !== '' && $body->isWritable()) {
                $body->write($captured);
            }

            (new SapiEmitter())->emit($response);
        } catch (Exception $e) {
            while (ob_get_level() >= $level) {
                ob_end_clean();
            }

            //TODO: Log instead of throwing
            throw $e;
        }
    }
}
