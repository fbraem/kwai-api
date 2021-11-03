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
        new Locale('nl'),
        new DocumentFormat('md'),
        'Test',
        'Test Summary',
        'Test Content',
        new Creator(
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
