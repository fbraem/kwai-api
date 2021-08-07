<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Kwai\Core\Infrastructure\Template\PlatesEngine;

/**
 * Class TemplateDependency
 */
class TemplateDependency implements Dependency
{
    public function __construct(
        private ?array $settings = null
    ) {
        $this->settings ??= depends('kwai.settings', Settings::class);
    }

    public function create()
    {
        $variables = [
            'website' => [
                'url' => $this->settings['website']['url'],
                'email' => $this->settings['website']['email'],
            ]
        ];
        return new PlatesEngine(__DIR__ . '/../../../../templates', $variables);
    }
}
