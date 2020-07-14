<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Modules\Pages\Domain\Application;
use Kwai\Modules\Pages\Domain\Author;
use Kwai\Modules\Pages\Domain\Page;
use Kwai\Modules\Users\Domain\ValueObjects\Username;

it(
    'can add content to a page',
    function () {
        $page = new Page((object)[
            'application' => new Entity(1, new Application((object)[
                'title' => 'Test'
            ]))
        ]);
        $page->addContent(new Text(
            new Locale('nl'),
            new DocumentFormat('md'),
            'Test',
            'Test Summary',
            'Test Content',
            new Entity(1, new Author((object)[
                'name' => new Username(
                    'Jigoro',
                    'Kono'
                )]))
        ));
        assertEquals(1, count($page->getContents()));
    }
);
