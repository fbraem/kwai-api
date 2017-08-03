<?php

namespace Core;

class Validator implements ValidatorInterface
{
    protected $validators;

    public function __construct()
    {
        $this->validators = [];
    }

    public function addValidator($path, $validator)
    {
        $this->validators[$path] = $validator;
    }

    public function validate($data)
    {
        $errors = [];

        foreach ($this->validators as $path => $validator) {
            $value = \JmesPath\search($path, $data);
            if (isset($value) && !$validator->isValid($value)) {
                $messages = [];
                foreach ($validator->getMessages() as $messageId => $message) {
                    $pointer = '/' + str_replace('.', '/', $path);
                    if (!isset($this->errors[$pointer])) {
                        $errors[$pointer] = [];
                    }
                    $errors[$pointer][] = $message;
                }
            }
        }

        return $errors;
    }
}
