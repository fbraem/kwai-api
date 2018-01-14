<?php

namespace Core\Responders;

use Psr\Http\Message\ResponseInterface;

class JSONErrorResponder implements ResponderInterface
{
    private $responder;

    private $errors;

    public function __construct(ResponderInterface $responder, $errors)
    {
        $this->responder = $responder;
        $this->errors = $errors;
    }

    public function respond() : ResponseInterface
    {
        $response = $this->responder->respond();
        $response = $response->withHeader('content-type', 'application/vnd.api+json');

        $errors = [];
        foreach($this->errors as $pointer => $messages) {
            foreach($messages as $message) {
                $errors[] = [
                    'source' => [
                        'pointer' => $pointer
                    ],
                    'title' => $message
                ];
            }
        }
        $data = json_encode([
            'errors' => $errors
        ]);
        $response->getBody()->write($data);

        return $response;
    }
}
