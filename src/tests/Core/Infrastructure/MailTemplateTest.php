<?php
/**
 * Testcase for Template
 */
declare(strict_types=1);

namespace Kwai\Tests\Core\Infrastructure;

use Kwai\Core\Infrastructure\Template\MailTemplate;
use PHPUnit\Framework\TestCase;
use Kwai\Core\Infrastructure\Template\PlatesEngine;

final class MailTemplateTest extends TestCase
{
    public function testTemplate(): void
    {
        $engine = new PlatesEngine(__DIR__);
        $mailTemplate = new MailTemplate(
            'Mail Template Test',
            $engine->createTemplate('html_template'),
            $engine->createTemplate('template')
        );
        $vars = [
          'name' => 'World'
        ];
        $text = $mailTemplate->renderPlainText($vars);
        $this->assertEquals('Hello World', $text);
        $html = $mailTemplate->renderHtml($vars);
        $this->assertEquals('Hello <b>World</b>', $html);
    }
}
