<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Mailer;

use Kwai\Core\Infrastructure\Configuration\MailerConfiguration;

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
