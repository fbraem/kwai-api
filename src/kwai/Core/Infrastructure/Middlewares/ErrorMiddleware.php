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
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Class ErrorMiddleware
 *
 * Middleware to handle errors. Make sure this middleware is the first to add
 * in the queue.
 */
class ErrorMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private ?AnalysisResultInterface $corsAnalysis = null,
        private ?LoggerInterface $logger = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $error) {
            $response = $this->responseFactory->createResponse(500, 'Internal Server Error');

            // Check if we need to add cors headers
            if ($this->corsAnalysis) {
                $corsRequestType = $this->corsAnalysis->getRequestType();
                if ($corsRequestType == AnalysisResultInterface::TYPE_PRE_FLIGHT_REQUEST
                    || $corsRequestType == AnalysisResultInterface::TYPE_ACTUAL_REQUEST) {
                    $response = $this->withCorsHeaders($response);
                }
            }

            $json = [
                'type' => get_class($error),
                'code' => $error->getCode(),
                'message' => $error->getMessage(),
                'stack' => collect($error->getTrace())
                    ->map(fn($item) => collect($item)->forget('type'))
                    ->toArray()
            ];
            if ($this->logger) {
                $this->logger->error('Exception occurred', $json);
            }
            // Unset the stack to avoid that it is returned to the client
            unset($json['stack']);

            $body = $this->streamFactory->createStream(json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return $response->withBody($body)->withHeader('Content-Type', '');
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
