<?php
/**
 * @package Kwai
 * @subpackage Core
 * @author Franky Braem
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Template;

use League\Plates\Engine;

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
        if (is_dir($templatePath . '/Users')) {
            $this->engine->addFolder('Users', $templatePath . '/Users');
        }
        $this->engine->addData($variables);
    }

    /**
     * Creates a template.
     *
     * When a language is passed, it will search for <template>_<lang>.php
     *
     * When there is no specific template for the given language, a template file
     * without the language suffix is used.
     *
     * @param string $template
     * @param string $lang
     * @return Template
     */
    public function createTemplate(string $template, string $lang = 'nl'): Template
    {
        $languageTemplate = "${template}_${lang}";
        if ( $this->engine->exists($languageTemplate) ) {
            return new PlatesTemplate($this->engine, $languageTemplate);
        }
        return new PlatesTemplate($this->engine, $template);
    }
}
