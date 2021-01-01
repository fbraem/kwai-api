<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types=1);

namespace Kwai\Modules\Mails\Domain\Exceptions;

use Exception;

/**
 * Class MailNotFoundException
 *
 * Raised when a mail couldn't be found.
 */
class MailNotFoundException extends Exception
{
    private int $id;

    /**
     * MailNotFoundException constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct('Mail not found');
        $this->id = $id;
    }

    public function __toString()
    {
        return __CLASS__ . ': Mail(' . $this->id . ') not found';
    }
}
