<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Kwai\Core\Infrastructure\Configuration\Configuration;
use Kwai\Core\Infrastructure\Mailer\MailerService;
use Kwai\Core\Infrastructure\Mailer\MailerServiceFactory;

/**
 * Class MailerDependency
 */
class MailerDependency implements Dependency
{
    public function __construct(
        private ?Configuration $settings = null
    ) {
        $this->settings ??= depends('kwai.settings', Settings::class);
    }

    public function create(): MailerService
    {
        return (new MailerServiceFactory($this->settings->getMailerConfiguration()))->create();
    }
}
