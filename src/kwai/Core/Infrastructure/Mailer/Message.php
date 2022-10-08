<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

use Symfony\Component\Mime\Email;

/**
 * Interface for an email message
 */
interface Message
{
    /**
     * @param Email $email
     * @return Email
     */
    public function processMail(Email $email): Email;
}
