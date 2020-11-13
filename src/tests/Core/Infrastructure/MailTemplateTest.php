<?php
/**
 * Testcase for Template
 */
declare(strict_types=1);

namespace Tests\Core\Domain\Infrastructure;

use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Core\Infrastructure\Template\PlatesEngine;

it('can create a mail template', function () {
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
    expect($text)->toBe('Hello World');
    $html = $mailTemplate->renderHtml($vars);
    expect($html)->toBe('Hello <b>World</b>');
});
