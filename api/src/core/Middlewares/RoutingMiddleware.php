<?php

namespace Core\Middlewares;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

use Psr\Http\Message\ServerRequestInterface;

use Core\Responders\Responder;
use Core\Responders\HTTPCodeResponder;

/**
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
              $request = $request->withAttribute('clubman.route', $route);
              return $delegate->process($request);
          }
      }
      return (new HTTPCodeResponder(new Responder(), 501, "Route not found"))->respond();
    }
}
