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
        $filterParameters = $this->getFilterParameters($queryParameters);
        if ($filterParameters !== null) {
            $parameters['filter'] = $filterParameters;
        }
        $includeParameter = $this->getIncludeParameter($queryParameters);
        if ($includeParameter !== null) {
            $parameters['include'] = explode(',', $includeParameter);
        }

        $request = $request->withAttribute('parameters', $parameters);

        return $delegate->process($request);
    }

    private function getPageParameters($parameters)
    {
        return $this->getArrayParameter($parameters, 'page');
    }

    private function getFilterParameters($parameters)
    {
        return $this->getArrayParameter($parameters, 'filter');
    }

    private function getIncludeParameter($parameters)
    {
        return $this->getParameter($parameters, 'include');
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
