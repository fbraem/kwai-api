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
    public function __invoke(array $settings)
    {
        $variables = [
            'website' => [
                'url' => $settings['website']['url'],
                'email' => $settings['website']['email'],
            ]
        ];
        return new PlatesEngine(__DIR__ . '/../../../../templates', $variables);
    }
}
