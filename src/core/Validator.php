<?php

namespace Core;

class Validator implements ValidatorInterface
{
    protected $validators;

    protected $errors;

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
        $this->errors = [];

        foreach ($this->validators as $path => $validator) {
            $value = \JmesPath\search($path, $data);
            if (isset($value) && !$validator->isValid($value)) {
                $messages = [];
                foreach ($validator->getMessages() as $messageId => $message) {
                    $pointer = '/' . str_replace('.', '/', $path);
                    if (!isset($this->errors[$pointer])) {
                        $this->errors[$pointer] = [];
                    }
                    $this->errors[$pointer][] = $message;
                }
            }
        }

        return count($this->errors) == 0;
    }

    public function toJSON()
    {
        $errors = [];
        foreach ($this->errors as $pointer => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'source' => [
                        'pointer' => $pointer
                    ],
                    'title' => $message
                ];
            }
        }
        return json_encode(['errors' => $errors]);
    }
}
