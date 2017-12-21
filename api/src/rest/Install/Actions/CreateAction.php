<?php

namespace REST\Install\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\HTTPCodeResponder;

use League\Fractal;

class CreateAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $db = $request->getAttribute('clubman.container')['db'];
        $users = new \Domain\User\UsersTable($db);
        if ($users->count() == 0) {
            $data = $payload->getInput();

            $validator = new \REST\Users\UserValidator();
            $errors = $validator->validate($data);
            if (count($errors) > 0) {
                return (new JSONErrorResponder(new Responder(), $errors))->respond();
            }

            $attributes = \JmesPath\search('data.attributes', $data);
            $user = new \Domain\User\User($db, [
                'email' => $attributes['email'],
                'password' => password_hash($attributes['password'], PASSWORD_DEFAULT)
            ]);
            $user->store();

            $payload->setOutput(new Fractal\Resource\Item($user, new \Domain\User\UserTransformer, 'users'));
            return (new JSONResponder(new HTTPCodeResponder(new Responder(), 201), $payload))->respond();
        }

        return (new HTTPCodeResponder(new Responder(), 403, _('Installation is already done')))->respond();
    }
}
