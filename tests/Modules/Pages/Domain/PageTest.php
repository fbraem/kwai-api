<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Modules\Pages\Domain\Application;
use Kwai\Modules\Pages\Domain\Page;

it('can add content to a page', function () {
    $page = new Page(
        application: new Entity(
            1,
            new Application(
                title: 'Test',
                name: 'test'
            )
        )
    );
    $page->addContent(new Text(
        locale: new Locale('nl'),
        format: new DocumentFormat('md'),
        title: 'Test',
        summary:'Test Summary',
        content: 'Test Content',
        author: new Creator(
            1,
            new Name(
                'Jigoro',
                'Kano'
            )
        )
    ));

    expect($page->getContents())
        ->toHaveCount(1)
    ;
});
