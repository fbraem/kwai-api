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
    /**
     * Create a template
     * @param string $template
     * @return Template
     */
    public function createTemplate(string $template): Template;
}
