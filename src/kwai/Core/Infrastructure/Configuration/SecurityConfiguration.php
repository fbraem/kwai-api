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

    public function __construct(
        string|array|null $relaxed,
        private string $secret,
        private string $algorithm = 'HS256',
        private string $header = 'Authorization',
        private string $regex = '/Bearer\s+(.*)$/i',
        private string $cookie = 'token'
    ) {
        if (is_string($relaxed)) {
            $this->relaxed = array_map(
                static fn($element) => trim($element),
                explode(',', $relaxed)
            );
        } elseif (is_array($relaxed)) {
            $this->relaxed = $relaxed;
        }
    }
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

    public static function createFromVariables(array $variables): self
    {
        $args = [];
        $parameters = [
            'KWAI_SECURITY_RELAXED' => 'relaxed',
            'KWAI_SECURITY_SECRET' => 'secret',
            'KWAI_SECURITY_ALGORITHM' => 'algorithm',
            'KWAI_SECURITY_HEADER' => 'header',
            'KWAI_SECURITY_REGEX' => 'regex',
            'KWAI_SECURITY_COOKIE' => 'cookie'
        ];
        foreach($parameters as $variableName => $argName) {
            if (isset($variables[$variableName])) {
                $args[$argName] = $variables[$variableName];
            }
        }
        return new self(...$args);
    }

    public static function validate(Dotenv $env): void
    {
        $env->required([
            'KWAI_SECURITY_SECRET'
        ]);
    }
}
