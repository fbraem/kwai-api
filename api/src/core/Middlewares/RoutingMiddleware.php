<?php

namespace Core\Middlewares;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

use Psr\Http\Message\ServerRequestInterface;

use Core\Responders\Responder;
use Core\Responders\HTTPCodeResponder;

/**
 * Middleware that is responsible of instantiating a router class based on then
 * first part of the URI.
 */
class RoutingMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        DelegateInterface $delegate
    ) {
      $domainPath = $request->getAttribute('clubman.domainpath');

      $parts = explode('/', $domainPath);

      if (count($parts) > 0) { // Sport, for example : judo/member
          if ($parts[0] == 'sport') {
              //TODO
          } else {
              $routerClassName = '\\REST\\' . ucfirst($parts[0]) . '\\Router';
          }
      }

      if (class_exists($routerClassName)) {
          $router = new $routerClassName();
          $route = $router->match($request);
          if ($route) {
              // add route attributes to the request
              foreach ($route->attributes as $key => $val) {
                  $request = $request->withAttribute('route.' . $key, $val);
              }
              $request = $request->withAttribute('clubman.route', $route);
              return $delegate->process($request);
          }
      }
      return (new HTTPCodeResponder(new Responder(), 501, "Route not found"))->respond();
    }
}
