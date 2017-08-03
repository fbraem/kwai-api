<?php

namespace Core;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

/**
 * The Zend Authentication adapter.
 */
class AuthAdapter implements AdapterInterface
{
    /**
     * @var The email address to use for authentication
     */
    private $email;

    /**
     * @var The password
     */
    private $password;

    /**
     * Sets username and password for Authentication
     */
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *     If authentication cannot be performed
     */
    public function authenticate()
    {
        $userRepo = new \Domain\User\UserRepository();
        $user = $userRepo->findWithEmail($this->email);

        if ($user) {
            //TODO: add activation
            //$activation = $user->activation;
            //if ($activation != null && $activation->completed) {
                if (password_verify($this->password, $user->password)) {
                    return new Result(Result::SUCCESS, $user);
                } else {
                    return new Result(
                        Result::FAILURE_CREDENTIAL_INVALID,
                        null,
                        [ _("Invalid password")]
                    );
                }
            /*
            } else {
                return new Result(
                    Result::FAILURE_UNCATEGORIZED,
                    null,
                    [ _("User not active")]
                );
            }
            */
        }
        return new Result(
            Result::FAILURE_IDENTITY_NOT_FOUND,
            null,
            [_('User doesn\'t exist')]
        );
    }
}
