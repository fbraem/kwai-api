<?php
namespace Core;

use Zend\Diactoros\Response;
use Zend\Diactoros\Server;
use Zend\Stratigility\MiddlewarePipe;
use Zend\Stratigility\NoopFinalHandler;

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

        $dbConnection = $this->config->database->{$this->config->default_database};
        $dbConnectionArray = $dbConnection->toArray();
        $dbConnectionArray['driver'] = $dbConnection->adapter;
        $dbConnectionArray['database'] = $dbConnection->name;
        $dbConnectionArray['username'] = $dbConnection->user;
        $dbConnectionArray['password'] = $dbConnection->pass;
        $analogue = new \Analogue\ORM\Analogue($dbConnectionArray);
        $analogue->registerPlugin('Analogue\ORM\Plugins\Timestamps\TimestampsPlugin');

        $analogue->connection()->setEventDispatcher(new \Illuminate\Events\Dispatcher(new \Illuminate\Container\Container));
        $analogue->connection()->listen(function ($query) {
            //TODO: Move the creation of the logger to application
            // and move the name of the file to the configuration
            $writer = new \Zend\Log\Writer\Stream('../tmp/clubman.sql');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer, 7);
            $logger->info($query->sql);
        });
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

        $app->pipe(new Middlewares\PathMiddleware());
        $app->pipe(new Middlewares\RoutingMiddleware());
        $app->pipe(new Middlewares\AuthorityMiddleware($this->config->jwt));
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
