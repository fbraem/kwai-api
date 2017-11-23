<?php

namespace REST\Install\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\HTTPCodeResponder;

use League\Fractal;

class CheckAction implements \Core\ActionInterface
{
  public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
  {
      $userRepo = new \Domain\User\UserRepository();
      if ($userRepo->count() == 0) {
          return (new HTTPCodeResponder(new Responder(), 200))->respond();
      }
      return (new HTTPCodeResponder(new Responder(), 403, _('Installation is already done')))->respond();
  }
}
