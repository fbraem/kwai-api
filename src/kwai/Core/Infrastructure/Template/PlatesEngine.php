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
     * @param array $variables Variables that are available for all templates
     */
    public function __construct(string $templatePath, array $variables = [])
    {
        $this->engine = new Engine($templatePath);
        $this->engine->addFolder('root', $templatePath);
        $this->engine->addFolder('Users', $templatePath . '/Users');
        $this->engine->addData($variables);
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
