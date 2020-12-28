<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Middlewares;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
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
        private StreamFactoryInterface $streamFactory
    ) {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch(Throwable $error) {
            $json = [
                'type' => get_class($error),
                'code' => $error->getCode(),
                'message' => $error->getMessage(),
                'stack' =>
                    collect($error->getTrace())
                    ->map(fn($item) => collect($item)->forget('type'))
                    ->toArray()
            ];
            $response = $this->responseFactory->createResponse(500, 'Internal Server Error');
            $body = $this->streamFactory->createStream(json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return $response->withBody($body)->withHeader('Content-Type', '');
        }
    }
}
