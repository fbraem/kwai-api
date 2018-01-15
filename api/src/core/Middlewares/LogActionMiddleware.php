<?php

namespace Core\Middlewares;

use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

use Aura\Payload\Payload;

/**
 * Middleware that is responsible for logging the action
 */
class LogActionMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $delegate
    ) {
        $user = $request->getAttribute('clubman.user');
        if ($user) {
            $route = $request->getAttribute('clubman.route');
            $db = $request->getAttribute('clubman.container')['db'];
            $this->table = new \Zend\Db\TableGateway\TableGateway('user_logs', $db);
            $this->table->insert([
                'user_id' => $user->id(),
                'action' => $route->name,
                'rest' => $route->extras['rest'] ?? '',
                'model_id' => $route->attributes['id'] ?? 0,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);
        }
        return $delegate->process($request);
    }
}
