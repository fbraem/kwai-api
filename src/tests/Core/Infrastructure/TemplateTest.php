<?php
/**
 * Testcase for Template
 */
declare(strict_types=1);

namespace Kwai\Tests\Core\Infrastructure;

use PHPUnit\Framework\TestCase;
use Kwai\Core\Infrastructure\Template\PlatesEngine;

final class TemplateTest extends TestCase
{
    public function testTemplate(): void
    {
        $engine = new PlatesEngine(__DIR__);
        $template = $engine->createTemplate('template');
        $text = $template->render([ 'name' => 'World' ]);
        $this->assertEquals('Hello World', $text);
    }
}
