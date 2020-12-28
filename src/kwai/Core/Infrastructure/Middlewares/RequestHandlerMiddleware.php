<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Middlewares;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

/**
 * Class RequestHandlerMiddleware
 */
class RequestHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ContainerInterface $container,
        private ResponseInterface $response
    ) {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $action = $request->getAttribute('kwai.action');

        if (is_callable($action)) {
            $action = $action($this->container);
            return $action($request, $this->response, $request->getAttribute('kwai.action.args'));
        }

        throw new RuntimeException('Invalid request handler set');
    }
}
