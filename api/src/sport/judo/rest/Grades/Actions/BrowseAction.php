<?php

namespace Judo\REST\Grades\Actions;

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
        $grades = (new \Judo\Domain\Member\GradesTable($db))->find();
        $payload->setOutput(new Fractal\Resource\Collection($grades, new \Judo\Domain\Member\GradeTransformer(), 'sport_judo_grades'));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
