<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use Exception;

/**
 * This exception is thrown when an exception occurs with SmtpMailerService.
 */
class MailerException extends Exception
{
    /**
     * Constructor
     *
     * @param string         $message
     * @param Exception|null $exception Original exception
     */
    public function __construct(
        string $message,
        private ?Exception $exception = null
    ) {
        parent::__construct($message);
        $this->exception = $exception;
    }

    public function __toString()
    {
        $str = __CLASS__ . ': ' . $this->getMessage();
        if ($this->exception) {
            $str .= '(' . $this->exception->getMessage() . ')';
        }
        return $str;
    }
}
