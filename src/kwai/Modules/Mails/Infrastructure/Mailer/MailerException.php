<?php
/**
 * NotFoundException
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

/**
 * This exception is thrown when an exception occurs with SmtpMailerService.
 */
class MailerException extends \Exception
{
    /**
     * Original exception
     * @var \Exception
     */
    private $exception;

    /**
     * Constructor
     * @param string $message
     * @param \Exception $exception Original exception
     */
    public function __construct(string $message, \Exception $exception = null)
    {
        parent::__construct($message);
        $this->exception = $exception;
    }

    public function __toString()
    {
        $s = __CLASS__ . ': ' . $this->getMessage();
        if ($this->exception) {
            $s .= '(' . $this->exception->getMessage() . ')';
        }
        return $s;
    }
}
