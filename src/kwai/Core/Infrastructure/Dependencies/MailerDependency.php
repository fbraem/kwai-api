<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Kwai\Modules\Mails\Infrastructure\Mailer\MailerServiceFactory;

/**
 * Class MailerDependency
 */
class MailerDependency implements Dependency
{
    public function __construct(
        private ?array $settings = null
    ) {
        $this->settings ??= depends('kwai.settings', Settings::class);
    }

    public function create()
    {
        return (new MailerServiceFactory())->create(
            $this->settings['mail']['url']
        );
    }
}
