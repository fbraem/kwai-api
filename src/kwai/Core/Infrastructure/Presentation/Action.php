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
     * Execute the action.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return mixed
     */
    abstract public function __invoke(Request $request, Response $response, array $args);
}
