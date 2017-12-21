<?php

namespace REST\Users;

use Zend\Validator\ValidatorChain;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class UserValidator implements \Core\ValidatorInterface
{
    private $validator;

    public function __construct()
    {
        $this->validator = new \Core\Validator();
    }

    public function validate($data)
    {
        $validators = [];

        $emailValidation = new ValidatorChain();
        $emailValidation->attach(
            (new NotEmpty(NotEmpty::STRING))->setMessages(
                [NotEmpty::IS_EMPTY => _('Email is required')]
            ),
            true // Stop validation on empty field
        );
        $email = new EmailAddress();
        $emailValidation->attach($email);
        $email->setMessages([
            EmailAddress::INVALID_FORMAT =>
            _("The input is not a valid email address.")
        ]);
        $this->validator->addValidator('data.attributes.email', $emailValidation);

        $firstNameValidation = new StringLength(['max' => 255]);
        $firstNameValidation->setMessage(
            _('Firstname can\'t contain more then 255 characters'),
            StringLength::TOO_LONG
        );
        $this->validator->addValidator('data.attributes.first_name', $firstNameValidation);

        $lastNameValidation = new StringLength(['max' => 255]);
        $lastNameValidation->setMessage(
            _('Lastname can\'t contain more then 255 characters'),
            StringLength::TOO_LONG
        );
        $this->validator->addValidator('data.attributes.last_name', $lastNameValidation);

        $pwd = \JmesPath\search('data.attributes.pwd', $data);
        if (isset($pwd)) {
            $pwdValidation = new StringLength(['min' => 8, 'max' => 255]);
            $pwdValidation->setMessage(
                _('Password can\'t contain more then 255 characters'),
                StringLength::TOO_LONG
            );
            $pwdValidation->setMessage(
                _('Password must contain at least 8 characters'),
                StringLength::TOO_SHORT
            );
            $this->validator->addValidator('data.attributes.pwd', $pwdValidation);
        }

        return $this->validator->validate($data);
    }
}
