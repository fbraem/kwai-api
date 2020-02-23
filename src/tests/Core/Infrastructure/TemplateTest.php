<?php
/**
 * Testcase for Template
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Core\Infrastructure\Template\PlatesEngine;

final class TemplateTest extends TestCase
{
    public function testTemplate(): void
    {
        $engine = new PlatesEngine(__DIR__);

        $text = $engine->render('template', [ 'name' => 'World' ]);
        $this->assertEquals($text, 'Hello World');
    }
}
