<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use Kwai\Core\Infrastructure\Configuration\DsnConfiguration;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;

/**
 * Class MailerConfiguration
 */
class MailerConfiguration
{
    public function __construct(
        private DsnConfiguration $dsn,
        private string|array $from
    ) {
    }

    public function getDsn(): DsnConfiguration
    {
        return $this->dsn;
    }

    public function getFromAddress(): Address {
        return Address::create($this->from);
    }
}
