<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Template;

/**
 * Interface for a template
 */
interface Template
{
    /**
     * Render the template
     * @param array $vars
     * @return string
     */
    public function render(array $vars): string;
}
