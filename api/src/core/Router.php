<?php
namespace Core;

use Psr\Http\Message\RequestInterface;

abstract class Router
{
    protected $container;

    protected $map;

    public function __construct()
    {
        $this->container = new \Aura\Router\RouterContainer();
        $this->map = $this->container->getMap();

        $this->map->tokens([
            'id' => '\d+'
        ]);

        $this->createRoutes();
    }

    abstract public function createRoutes();

    public function match(RequestInterface $request)
    {
        $matcher = $this->container->getMatcher();
        return $matcher->match($request);
    }
}
