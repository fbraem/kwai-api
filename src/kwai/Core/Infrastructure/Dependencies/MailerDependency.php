<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Kwai\Core\Infrastructure\Configuration\Configuration;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Mailer\DatabaseMailerService;
use Kwai\Core\Infrastructure\Mailer\MailerService;
use Kwai\Core\Infrastructure\Mailer\MailerServiceFactory;
use Kwai\Modules\Mails\Infrastructure\Repositories\MailDatabaseRepository;

/**
 * Class MailerDependency
 */
class MailerDependency implements Dependency
{
    public function __construct(
        private ?Configuration $settings = null,
        private ?Connection $db = null
    ) {
        $this->settings ??= depends('kwai.settings', Settings::class);
        $this->db ??= depends('kwai.db', DatabaseDependency::class);
    }

    public function create(): MailerService
    {
        return new DatabaseMailerService(
            new MailDatabaseRepository($this->db),
            (new MailerServiceFactory($this->settings->getMailerConfiguration()))
                ->create()
        );
    }
}
