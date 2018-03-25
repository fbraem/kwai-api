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
        $members = \Judo\Domain\Member\MembersTable::getTableFromRegistry()
            ->find()
            ->contain(['Person', 'Person.Contact', 'Person.Nationality'])
            ->order([
                'Person.lastname' => 'ASC',
                'Person.firstname' => 'ASC'])
            ->all();
        $payload->setOutput(new Fractal\Resource\Collection($members, new \Judo\Domain\Member\MemberTransformer(), 'sport_judo_members'));
        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
