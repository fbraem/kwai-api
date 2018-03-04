<?php

namespace Judo\REST\Members\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

use League\Fractal;

class BrowseAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $db = $request->getAttribute('clubman.container')['db'];
        $members = (new \Judo\Domain\Member\MembersTable($db))->orderByName()->find();
        $payload->setOutput(new Fractal\Resource\Collection($members, new \Judo\Domain\Member\MemberTransformer(), 'sport_judo_members'));
        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
