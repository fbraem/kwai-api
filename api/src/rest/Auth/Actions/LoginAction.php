<?php

namespace REST\Auth\Actions;

use Psr\Http\Message\RequestInterface;
use Aura\Payload\Payload;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\NonPersistent;

use Core\Responders\Responder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\JSONResponder;
use Core\Responders\NotFoundResponder;
use Core\Responders\HTTPCodeResponder;

use League\Fractal;

class LoginAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload)
    {
        $responder = new Responder();
        $data = $payload->getInput()['data'];

        $attributes = \JmesPath\search('data.attributes', $data);
        if (empty($attributes['email'])) {
          return new JSONErrorResponder(new HTTPCodeResponder($responder, 400),
            [
              [
                'title' => _('Email is required'),
                'source' => [
                  'pointer' => '/data/attributes/email'
                ]
              ]
            ]
          );
        }
        if (empty($attributes['password'])) {
          return new JSONErrorResponder(new HTTPCodeResponder($responder, 400),
            [
              [
                'title' => _('Password is required'),
                'source' => [
                  'pointer' => '/data/attributes/password'
                ]
              ]
            ]
          );
        }

        $auth = $request->getAttribute('clubman.authenticationService');
        $authAdapter = new \Core\AuthAdapter(
            $attributes['email'],
            $attributes['password']
        );
        $result = $auth->authenticate($authAdapter);

        if ($result->isValid()) {
            $user = $result->getIdentity();
            $user->last_login = \Carbon\Carbon::now();
            $userRepo = new \Domain\User\UserRepository();
            $userRepo->store($user);

            $payload->setOutput(new Fractal\Resource\Item($user, new \Domain\User\UserTransformer, 'users'));
            $payload->setExtras([
                'jwt' => $auth->getStorage()->getJWT()
            ]);
            return new JSONResponder($responder, $payload);
        } else {
            return new NotFoundResponder($responder, $result->getMessages()[0]);
        }

        return new HTTPCodeResponder($responder, 400, _('Data is missing'));
    }
}
