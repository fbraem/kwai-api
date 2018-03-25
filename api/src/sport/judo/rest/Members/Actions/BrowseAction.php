<?php

namespace Judo\REST\Members\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

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
        $payload->setOutput(\Judo\Domain\Member\MemberTransformer::createForCollection($members));
        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
