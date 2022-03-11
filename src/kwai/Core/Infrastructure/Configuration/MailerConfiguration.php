<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Configuration;

use Dotenv\Dotenv;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;

/**
 * Class MailerConfiguration
 */
class MailerConfiguration implements Configurable
{
    private DsnConfiguration $dsn;

    private string|array $from;

    public function getDsn(): DsnConfiguration
    {
        return $this->dsn;
    }

    public function getFromAddress(): Address {
        return Address::create($this->from);
    }

    public function load(array $variables): void
    {
        $this->dsn = new DsnConfiguration($variables['KWAI_MAIL_DSN']);
        if (isset($variables['KWAI_MAIL_FROM_NAME'])) {
            $this->from = [
                $variables['KWAI_MAIL_FROM'] => $variables['KWAI_MAIL_FROM_NAME']
            ];
        } else {
            $this->from = $variables['KWAI_MAIL_FROM'];
        }
    }

    public function validate(Dotenv $env): void
    {
        $env->required([
            'KWAI_MAIL_DSN',
            'KWAI_MAIL_FROM'
        ]);
    }
}
