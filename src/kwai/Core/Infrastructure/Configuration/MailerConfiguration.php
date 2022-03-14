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
    /**
     * @param DsnConfiguration $dsn
     * @param Address          $from
     */
    public function __construct(
        private DsnConfiguration $dsn,
        private Address $from
    ) {
    }

    public function getDsn(): DsnConfiguration
    {
        return $this->dsn;
    }

    public function getFromAddress(): Address {
        return $this->from;
    }

    public static function validate(Dotenv $env): void
    {
        $env->required([
            'KWAI_MAIL_DSN',
            'KWAI_MAIL_FROM'
        ]);
    }

    public static function createFromVariables(array $variables): self
    {
        if (isset($variables['KWAI_MAIL_FROM_NAME'])) {
            $from = [
                $variables['KWAI_MAIL_FROM'] => $variables['KWAI_MAIL_FROM_NAME']
            ];
        } else {
            $from = $variables['KWAI_MAIL_FROM'];
        }
        return new self(
            new DsnConfiguration($variables['KWAI_MAIL_DSN']),
            new Address($from)
        );
    }
}
