<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Kwai\Core\Infrastructure\Configuration\Configuration;
use Kwai\Core\Infrastructure\Template\PlatesEngine;

/**
 * Class TemplateDependency
 */
class TemplateDependency implements Dependency
{
    public function __construct(
        private ?Configuration $settings = null
    ) {
        $this->settings ??= depends('kwai.settings', Settings::class);
    }

    public function create(): PlatesEngine
    {
        $websiteConfiguration = $this->settings->getWebsiteConfiguration();
        $variables = [
            'website' => [
                'name' => $websiteConfiguration->getName(),
                'url' => $websiteConfiguration->getUrl(),
                'email' => $websiteConfiguration->getAddress()->getEmail(),
            ]
        ];
        return new PlatesEngine(__DIR__ . '/../../../../templates', $variables);
    }
}
