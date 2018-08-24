<?php

namespace Core\Validators;

use Zend\Validator\NotEmpty;

class EmptyValidator extends InputValidator
{
    public function __construct()
    {
        parent::__construct();
    }

    public function validate($data)
    {
        $this->errors = [];

        foreach ($this->validators as $path => $validator) {
            $value = \JmesPath\search($path, $data);
            if (!$validator->isValid($value)) {
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

    public function addValidator($path, $option = null)
    {
        $notEmpty = new NotEmpty($option);
        $notEmpty->setMessage(_("Value can't be empty"));
        parent::addValidator($path, $notEmpty);
    }
}
