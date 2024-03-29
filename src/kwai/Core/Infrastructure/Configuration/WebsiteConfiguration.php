<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Configuration;

use Dotenv\Dotenv;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;

/**
 * Class WebsiteConfiguration
 *
 * Configuration for the website.
 */
class WebsiteConfiguration implements Configurable
{
    public function __construct(
        private readonly string $name,
        private readonly string $url,
        private readonly Address $email
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getAddress(): Address
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function createFromVariables(array $variables): self
    {
        return new self(
            $variables['KWAI_WEBSITE_NAME'] ?? '',
            $variables['KWAI_WEBSITE_URL'],
            new Address(
                new EmailAddress($variables['KWAI_WEBSITE_EMAIL']),
                $variables['KWAI_WEBSITE_EMAIL_FROM'] ?? ''
            )
        );
    }

    public static function validate(Dotenv $env): void
    {
        $env->required([
            'KWAI_WEBSITE_URL',
            'KWAI_WEBSITE_EMAIL'
        ]);
    }
}
