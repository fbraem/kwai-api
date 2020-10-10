<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class Action
 *
 * Base class for all actions.
 */
abstract class Action
{
    /**
     * The dependency container
     */
    private ContainerInterface $container;

    /**
     * Action constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Returns an entry from the container
     * @param string $key
     * @return mixed
     */
    public function getContainerEntry(string $key)
    {
        return $this->container->get($key);
    }

    /**
     * Log the message (when there is a logger configured). The logger must
     * implement PSR-3 standard.
     *
     * @param string $level
     * @param string $message
     */
    public function log(string $level, string $message)
    {
        /** @var LoggerInterface $logger */
        $logger = $this->getContainerEntry('logger');
        if ($logger) {
            $logger->log($level, $message);
        }
    }

    /**
     * Logs an exception with level ERROR
     *
     * @param $exception
     */
    public function logException($exception)
    {
        $this->log(LogLevel::ERROR, strval($exception));
    }

    /**
     * Execute the action.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return mixed
     */
    abstract public function __invoke(Request $request, Response $response, array $args);
}
