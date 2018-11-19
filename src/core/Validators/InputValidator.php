<?php

namespace Core\Validators;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;

class InputValidator implements ValidatorInterface
{
    private $validators;

    public function __construct($validators, $optional = false)
    {
        foreach ($validators as $path => $validator) {
            if (!is_array($validator)) {
                $validator = [ $validator, $optional ];
            }
            $paths = explode('.', $path);
            $validator[0]->setName(end($paths));
            $this->validators[$path] = $validator;
        }
    }

    public function validate($data)
    {
        $errors = [];

        foreach ($this->validators as $path => $v) {
            $value = \JmesPath\search($path, $data);
            if ($v[1] && is_null($value)) {
                continue;
            }

            try {
                $v[0]->assert($value);
            } catch (NestedValidationException $nve) {
                $messages = $nve->getMessages();
                foreach ($messages as $message) {
                    $pointer = '/' . str_replace('.', '/', $path);
                    if (!isset($this->errors[$pointer])) {
                        $errors[$pointer] = [];
                    }
                    $errors[$pointer][] = $message;
                }
            }
        }

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
    }
}
