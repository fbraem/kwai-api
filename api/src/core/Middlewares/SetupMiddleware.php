<?php

namespace Core\Middlewares;

use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This middleware determines the root path (When clubman is installed in a
 * subfolder of the document root, all these subfolders must be
 * removed from the URI path for the RouterMiddleware.), sets some
 * configuration, ...
 */
class SetupMiddleware implements MiddlewareInterface
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Gets the base folder of clubman
     */
    private function getBase($serverParams)
    {
        $tempPath1 = explode(
            '/',
            str_replace('\\', '/', dirname($serverParams['SCRIPT_FILENAME']))
        );
        $tempPath2 = explode(
            '/',
            substr(CLUBMAN_ABSPATH, 0, -1)
        );
        $tempPath3 = explode(
            '/',
            str_replace('\\', '/', dirname($serverParams['PHP_SELF']))
        );
        for ($i = count($tempPath2); $i < count($tempPath1); $i++) {
            array_pop($tempPath3);
        }
        $urladdr = implode('/', $tempPath3);
        if (substr($urladdr, -1) === '/') {
            return $baseurl = $urladdr;
        }
        return $urladdr . '/';
    }

    /**
     * Determine baseurl and domainpath and remove baseurl from the path
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $delegate
    ) {
        $request = $request->withAttribute('clubman.config', $this->app->getConfig());
        $request = $request->withAttribute('clubman.container', $this->app->getContainer());

        $baseurl = $this->getBase($request->getServerParams());
        $uri = $request->getUri();
        $domainPath = substr($uri->getPath(), strlen($baseurl));

        $request = $request->withAttribute('clubman.baseurl', $baseurl);
        $request = $request->withAttribute('clubman.domainpath', $domainPath);

        $originalPath = $request->getUri()->getPath();
        $newPath = substr($originalPath, strlen($baseurl));
        $uri = $request->getUri()->withPath('/' . $newPath);
        $request = $request->withUri($uri);

        return $delegate->process($request);
    }
}
