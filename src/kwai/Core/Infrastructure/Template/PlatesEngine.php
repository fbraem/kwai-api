<?php
/**
 * @package Kwai
 * @subpackage Core
 * @author Franky Braem
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Template;

use \League\Plates\Engine;

/**
 * Class PlatesEngine
 * Renders a template using Plate.
 * @see https://platesphp.com/
 */
class PlatesEngine implements TemplateEngine
{
    /**
     * The template engine
     */
    private Engine $engine;

    /**
     * PlatesEngine constructor.
     * @param string $templatePath The path where templates are located.
     */
    public function __construct(string $templatePath)
    {
        $this->engine = new Engine($templatePath);
    }

    /**
     * Creates a template
     * @param string $template
     * @return Template
     */
    public function createTemplate(string $template): Template
    {
        return new PlatesTemplate($this->engine, $template);
    }
}
