<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Configuration;

use Dotenv\Dotenv;

/**
 * Class WebsiteConfiguration
 *
 * Configuration for the website.
 */
class WebsiteConfiguration implements Configurable
{
    private string $url;

    private string $email;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function load(array $variables): void
    {
        $this->url = $variables['KWAI_WEBSITE_URL'];
        $this->email = $variables['KWAI_WEBSITE_EMAIL'];
    }

    public function validate(Dotenv $env): void
    {
        $env->required([
            'KWAI_WEBSITE_URL',
            'KWAI_WEBSITE_EMAIL'
        ]);
    }
}
