<?php

namespace REST\Users;

use Core\Validators\InputValidator;
use Zend\Validator\ValidatorChain;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class UserValidator extends InputValidator
{
    public function __construct()
    {
        parent::__construct();

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
        $this->addValidator('data.attributes.email', $emailValidation);

        $firstNameValidation = new StringLength(['max' => 255]);
        $firstNameValidation->setMessage(
            _('Firstname can\'t contain more then 255 characters'),
            StringLength::TOO_LONG
        );
        $this->addValidator('data.attributes.first_name', $firstNameValidation);

        $lastNameValidation = new StringLength(['max' => 255]);
        $lastNameValidation->setMessage(
            _('Lastname can\'t contain more then 255 characters'),
            StringLength::TOO_LONG
        );
        $this->addValidator('data.attributes.last_name', $lastNameValidation);

        $pwd = \JmesPath\search('data.attributes.pwd', $data);
        $pwdValidation = new StringLength(['min' => 8, 'max' => 255]);
        $pwdValidation->setMessage(
            _('Password can\'t contain more then 255 characters'),
            StringLength::TOO_LONG
        );
        $pwdValidation->setMessage(
            _('Password must contain at least 8 characters'),
            StringLength::TOO_SHORT
        );
        $this->addValidator('data.attributes.pwd', $pwdValidation);
    }
}
