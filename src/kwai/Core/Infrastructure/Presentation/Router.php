<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class Router
 *
 * A wrapper around Symfony Route
 */
class Router
{
    private RouteCollection $routes;

    /**
     * Router constructor.
     *
     * When autoPreflight is true (which is the default), an OPTIONS route will
     * be automatically added for each route.
     *
     * @param bool $autoPreflight
     */
    public function __construct(
        private bool $autoPreflight = true
    ) {
        $this->routes = new RouteCollection();
    }

    /**
     * Factory method
     *
     * @param bool $autoPreflight
     * @return static
     */
    public static function create(bool $autoPreflight = true): self
    {
        return new Router($autoPreflight);
    }

    /**
     * Add a GET route
     *
     * @param string          $name
     * @param string          $path
     * @param string|callable $handler
     * @param array           $extra
     * @param array           $requirements
     * @return $this
     */
    public function get(
        string          $name,
        string          $path,
        string|callable $handler,
        array           $extra = [],
        array           $requirements = []
    ) {
        return $this->add(
            $name,
            'GET',
            $path,
            $handler,
            $extra,
            $requirements
        );
    }

    /**
     * Add a POST route.
     *
     * @param string          $name
     * @param string          $path
     * @param string|callable $handler
     * @param array           $extra
     * @param array           $requirements
     * @return $this
     */
    public function post(
        string          $name,
        string          $path,
        string|callable $handler,
        array           $extra = [],
        array           $requirements = []
    ) {
        return $this->add(
            $name,
            'POST',
            $path,
            $handler,
            $extra,
            $requirements
        );
    }

    /**
     * Add a PATCH route.
     *
     * @param string          $name
     * @param string          $path
     * @param string|callable $handler
     * @param array           $extra
     * @param array           $requirements
     * @return $this
     */
    public function patch(
        string          $name,
        string          $path,
        string|callable $handler,
        array           $extra = [],
        array           $requirements = []
    )
    {
        return $this->add(
            $name,
            'PATCH',
            $path,
            $handler,
            $extra,
            $requirements
        );
    }

    /**
     * Add a DELETE method
     *
     * @param string          $name
     * @param string          $path
     * @param string|callable $handler
     * @param array           $extra
     * @param array           $requirements
     * @return $this
     */
    public function delete(
        string          $name,
        string          $path,
        string|callable $handler,
        array           $extra = [],
        array           $requirements = []
    )
    {
        return $this->add(
            $name,
            'DELETE',
            $path,
            $handler,
            $extra,
            $requirements
        );
    }

    /**
     * Add a OPTIONS route.
     *
     * Note: When autoPreflight is true, this route will be created
     * automatically. So there is no need to call this method.
     *
     * @param string          $name
     * @param string          $path
     * @param string|callable $handler
     * @param array           $extra
     * @param array           $requirements
     * @return $this
     */
    public function options(
        string          $name,
        string          $path,
        string|callable $handler,
        array           $extra = [],
        array           $requirements = []
    ) {
        return $this->add(
            $name,
            'OPTIONS',
            $path,
            $handler,
            $extra,
            $requirements
        );
    }

    /**
     * Add a HEAD route
     *
     * @param string          $name
     * @param string          $path
     * @param string|callable $handler
     * @param array           $extra
     * @param array           $requirements
     * @return $this
     */
    public function head(
        string          $name,
        string          $path,
        string|callable $handler,
        array           $extra = [],
        array           $requirements = []
    ) {
        return $this->add(
            $name,
            'HEAD',
            $path,
            $handler,
            $extra,
            $requirements
        );
    }

    /**
     * Add a group of routes to this router.
     *
     * @param string $path
     * @param Router $router
     * @return $this
     */
    public function group(string $path, Router $router)
    {
        $router->routes->addPrefix($path);
        $this->routes->addCollection($router->routes);
        return $this;
    }

    /**
     * Try to find a route for the request
     *
     * @param ServerRequestInterface $request
     * @return array
     * @throws RouteException
     */
    public function matchRequest(ServerRequestInterface $request): array
    {
        $symfonyRequest = (new HttpFoundationFactory())->createRequest($request);
        $context = new RequestContext();
        $context->fromRequest($symfonyRequest);

        $matcher = new UrlMatcher($this->routes, $context);
        try {
            $parameters = $matcher->matchRequest($symfonyRequest);
        } catch (Exception $e) {
            throw new RouteException(message: 'Could not find a route', previous: $e);
        }

        $route = $parameters['_route'] ?? null;
        unset($parameters['_route']);
        $handler = $parameters['_action'] ?? null;
        unset($parameters['_action']);
        $extra = $parameters['_extra'] ?? [];
        unset($parameters['_extra']);

        return [
            $route,
            $handler,
            $parameters,
            $extra
        ];
    }

    /**
     * Add a route
     *
     * @param string          $name
     * @param string          $method
     * @param string          $path
     * @param string|callable $handler
     * @param array           $extra
     * @param array           $requirements
     * @return $this
     */
    private function add(
        string          $name,
        string          $method,
        string          $path,
        string|callable $handler,
        array           $extra,
        array           $requirements
    ) {
        if ($this->autoPreflight) {
            $this->routes->add(
                $name . '.options',
                new Route(
                    path: $path,
                    defaults: [
                        '_action' => fn () => fn (Request $request, Response $response) => $response
                    ],
                    methods: ['OPTIONS'],
                    requirements: $requirements
                )
            );
        }
        $this->routes->add(
            $name,
            new Route(
                path: $path,
                defaults: [
                    '_action' => $handler,
                    '_extra' => $extra
                ],
                methods: [ $method ],
                requirements: $requirements
            )
        );
        return $this;
    }

    /**
     * Returns an associated array with all routes.
     *
     * @return array<string, string>
     */
    public function all()
    {
        $result = [];
        $routes = $this->routes->all();
        foreach ($routes as $name => $route) {
            $result[$name] = $route->getPath();
        }
        return $result;
    }
}
