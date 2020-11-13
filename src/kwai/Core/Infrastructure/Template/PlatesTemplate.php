<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Template;

use \League\Plates\Engine;

/**
 * PlatesTemplate
 */
class PlatesTemplate implements Template
{
    /**
     * The plates template engine
     */
    private Engine $engine;

    /**
     * Path to the template file.
     */
    private string $templateFile;

    /**
     * PlatesTemplate constructor.
     * @param Engine $engine
     * @param string $templateFile Path to the template file
     */
    public function __construct(Engine $engine, string $templateFile)
    {
        $this->engine = $engine;
        $this->templateFile = $templateFile;
    }

    /**
     * Renders the template
     * @param array $vars
     * @return string
     */
    public function render(array $vars): string
    {
        return $this->engine->render($this->templateFile, $vars);
    }
}