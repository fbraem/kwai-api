<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation;

use Exception;
use Kwai\Core\Infrastructure\Dependencies\LoggerDependency;
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
     * Action constructor.
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        private ?LoggerInterface $logger = null
    ) {
        $this->logger ??= depends('kwai.logger', LoggerDependency::class);
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
        if ($this->logger) {
            $this->logger->log($level, $message);
        }
    }

    /**
     * Logs an exception with level ERROR
     *
     * @param Exception $exception
     */
    public function logException(Exception $exception)
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
