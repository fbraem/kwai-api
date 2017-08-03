<?php

namespace REST\Install\Actions;

use Psr\Http\Message\RequestInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\HTTPCodeResponder;

use League\Fractal;

class CreateAction implements \Core\ActionInterface
{
  public function __invoke(RequestInterface $request, Payload $payload)
  {
      $userRepo = new \Domain\User\UserRepository();
      if ( $userRepo->count() == 0 ) {
          $data = $payload->getInput()['data'];

          $validator = new \Domain\User\UserValidator();
          $errors = $validator->validate($data);
          if (count($errors) > 0) {
              return new JSONErrorResponder(new Responder(), $errors);
          }

          $attributes = \JmesPath\search('data.attributes', $data);
          $user = new \Domain\User\User($attributes['email']);
          $user->password = password_hash($attributes['password'], PASSWORD_DEFAULT);
          $userRepo->store($user);

          $payload->setOutput(new Fractal\Resource\Item($user, new \Domain\User\UserTransformer, 'users'));
          return new JSONResponder(new HTTPCodeResponder(new Responder(), 201), $payload);
      }

      return new HTTPCodeResponder(new Responder(), 403, _('Installation is already done'));
  }
}
