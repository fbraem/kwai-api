<?php
/**
 * Testcase for Template
 */
declare(strict_types=1);

namespace Tests\Core\Domain\Infrastructure;

use Kwai\Core\Infrastructure\Template\PlatesEngine;

it('can render a template', function () {
    $engine = new PlatesEngine(__DIR__);
    $template = $engine->createTemplate('template');
    $text = $template->render([ 'name' => 'World' ]);
    expect($text)->toBe('Hello World');
});
