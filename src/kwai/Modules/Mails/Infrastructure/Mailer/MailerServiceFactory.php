<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types=1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use InvalidArgumentException;

/**
 * MailerServiceFactory
 */
class MailerServiceFactory
{
    public function __construct(private MailerConfiguration $config)
    {
    }

    /**
     * Creates a MailerService.
     *
     * @return MailerService
     */
    public function create(): MailerService
    {
        return new SymfonyMailerService($this->config);
    }
}
