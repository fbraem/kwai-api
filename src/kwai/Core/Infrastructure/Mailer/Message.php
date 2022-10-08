<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;

/**
 * Interface for an email message
 */
interface Message
{
    /**
     * @return MailContent
     */
    public function createMailContent(): MailContent;
}
