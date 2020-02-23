<?php
/**
 * @package Kwai
 * @subpackage Core
 * @author Franky Braem
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Template;

class PlatesEngine implements TemplateEngine
{
    private $engine;

    public function __construct(string $templatePath)
    {
        $this->engine = new \League\Plates\Engine($templatePath);
    }

    public function render(string $template, array $vars): string
    {
        return $this->engine->render($template, $vars);
    }
}
