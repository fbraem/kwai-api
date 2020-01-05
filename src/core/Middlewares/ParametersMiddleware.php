<?php

namespace Core\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Middleware that is responsible of setting all parameter attributes.
 */
class ParametersMiddleware implements MiddlewareInterface
{
    public function process(
        Request $request,
        RequestHandler $handler
    ): ResponseInterface {
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
        $sortParameter = $this->getSortParameter($queryParameters);
        if ($sortParameter !== null) {
            $parameters['sort'] = explode(',', $sortParameter);
        }

        $request = $request->withAttribute('parameters', $parameters);

        return $handler->handle($request);
    }

    private function getPageParameters($parameters)
    {
        return $this->getArrayParameter($parameters, 'page');
    }

    private function getFilterParameters($parameters)
    {
        return $this->getArrayParameter($parameters, 'filter');
    }

    private function getSortParameter($parameters)
    {
        return $this->getParameter($parameters, 'sort');
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
