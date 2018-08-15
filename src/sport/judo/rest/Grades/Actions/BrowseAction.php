<?php

namespace Judo\REST\Grades\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

class BrowseAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $grades = \Judo\Domain\Member\GradesTable::getTableFromRegistry()->find()->all();
        $payload->setOutput(\Judo\Domain\Member\GradeTransformer::createForCollection($grades));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
