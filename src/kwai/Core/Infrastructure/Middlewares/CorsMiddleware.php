<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Middlewares;

use Neomerx\Cors\Contracts\AnalysisResultInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class CorsMiddleware
 *
 * Middleware that handles CORS
 */
class CorsMiddleware implements MiddlewareInterface
{
    /**
     * CorsMiddleware constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param AnalysisResultInterface  $corsAnalysis
     */
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private AnalysisResultInterface $corsAnalysis
    ) {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        switch ($this->corsAnalysis->getRequestType()) {
            case AnalysisResultInterface::ERR_NO_HOST_HEADER:
            case AnalysisResultInterface::ERR_ORIGIN_NOT_ALLOWED:
            case AnalysisResultInterface::ERR_METHOD_NOT_SUPPORTED:
            case AnalysisResultInterface::ERR_HEADERS_NOT_SUPPORTED:
                // Not allowed
                return $this->responseFactory->createResponse(403, 'CORS - Forbidden');
            case AnalysisResultInterface::TYPE_PRE_FLIGHT_REQUEST:
                // A preflight request
                $response = $this->responseFactory->createResponse(200);
                return $this->withCorsHeaders($response);
            case AnalysisResultInterface::TYPE_REQUEST_OUT_OF_CORS_SCOPE:
                // No cors
                return $handler->handle($request);
            default:
                // actual CORS request
                $response = $handler->handle($request);
                return $this->withCorsHeaders($response);
        }
    }

    /**
     * Adds cors headers to the response.
     *
     * @param ResponseInterface       $response
     * @return ResponseInterface
     */
    private function withCorsHeaders(
        ResponseInterface $response
    ): ResponseInterface {
        foreach ($this->corsAnalysis->getResponseHeaders() as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }
}
