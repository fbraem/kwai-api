<?php

namespace Core\Middlewares;

use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Middleware that is responsible of setting all parameter attributes.
 */
class ParametersMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $delegate
    ) {
        $parameters = [];
        $queryParameters = $request->getQueryParams();

        $pageParameters = $this->getPageParameters($queryParameters);
        if ($pageParameters !== null) {
            $parameters['page'] = $pageParameters;
        }

        $request = $request->withAttribute('parameters', $parameters);

        return $delegate->process($request);
    }

    private function getPageParameters($parameters)
    {
        $value = $this->getArrayParameter($parameters, 'page');
    }

    private function getFilterParameters($parameters)
    {
        $value = $this->getArrayParameter($parameters, 'filter');
    }

    private function getArrayParameter($parameters, $name)
    {
        $value = $this->getParameter($parameters, $name);

        if (($value === null) || is_array($value) === true) {
            return $value;
        }
        return null;
    }

    private function getParameter($parameters, $name)
    {
        return isset($parameters[$name]) === true ? $parameters[$name] : null;
    }
}
