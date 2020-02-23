<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Template;

/**
 * Interface for a template engine
 */
interface TemplateEngine
{
    public function render(string $template, array $vars): string;
}
