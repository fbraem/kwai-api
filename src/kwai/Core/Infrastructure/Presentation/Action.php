<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation;

use Psr\Container\ContainerInterface;

/**
 * Action base class
 */
class Action
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
}
