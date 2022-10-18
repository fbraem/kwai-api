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
     * @param string $lang
     * @return Template
     */
    public function createTemplate(string $template, string $lang = 'nl'): Template;
}
