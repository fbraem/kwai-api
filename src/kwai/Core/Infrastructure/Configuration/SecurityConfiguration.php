<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Configuration;

use Dotenv\Dotenv;

/**
 * Class SecurityConfiguration
 *
 * Configuration for JWT security.
 */
class SecurityConfiguration implements Configurable
{
    private array $relaxed = [];

    private string $secret;

    private string $algorithm;

    private string $header;

    private string $regex;

    private string $cookie;

    /**
     * @return array
     */
    public function getRelaxed(): array
    {
        return $this->relaxed;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @return string
     */
    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * @return string
     */
    public function getCookie(): string
    {
        return $this->cookie;
    }

    public function load(array $variables): void
    {
        $relaxed = $variables['KWAI_SECURITY_RELAXED'] ?? null;
        if ($relaxed) {
            $this->relaxed = array_map(
                static fn($element) => trim($element),
                explode(',', $relaxed)
            );
        }

        $this->secret = $variables['KWAI_SECURITY_SECRET'];
        $this->algorithm = $variables['KWAI_SECURITY_ALGORITHM'] ?? 'HS256';
        $this->header = $variables['KWAI_SECURITY_HEADER'] ?? 'Authorization';
        $this->regex = $variables['KWAI_SECURITY_REGEX'] ?? '/Bearer\s+(.*)$/i';
        $this->cookie = $variables['KWAI_SECURITY_COOKIE'] ?? 'token';
    }

    public function validate(Dotenv $env): void
    {
        $env->required([
            'KWAI_SECURITY_SECRET'
        ]);
    }
}
