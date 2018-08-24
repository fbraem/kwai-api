<?php
namespace Core;

use Psr\Http\Message\ResponseInterface as Response;

class EntityValidator implements ValidatorInterface
{
    private $validators;

    private $errors;

    public function __construct()
    {
        $this->validators = [];
        $this->errors = [];
    }

    public function addValidator(\Zend\Validator\ValidatorInterface $validator)
    {
        $this->validators[] = $validator;
    }

    public function validate($value)
    {
        foreach ($this->validators as $validator) {
            if (!$validator->isValid($value)) {
                foreach ($validator->getMessages() as $messageId => $message) {
                    $this->errors[] = $message;
                }
            }
        }
        return count($this->errors) == 0;
    }

    public function unprocessableEntityResponse(Response $response) : Response
    {
        foreach ($this->errors as $error) {
            $errors[] = [
                'title' => $error
            ];
        }
        $response
            ->getBody()
            ->write(json_encode(['errors' => $errors]))
        ;

        return $response
            ->withStatus(422)
            ->withHeader('content-type', 'application/vnd.api+json')
        ;
    }
}
