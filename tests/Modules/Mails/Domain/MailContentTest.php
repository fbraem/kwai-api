<?php
declare(strict_types=1);

namespace Tests\Modules\Mails\Domain;

use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;

it('can create an email with HTML', function () {
    $mailContent = new MailContent('Test', 'TEST', '<b>TEST</b>');
    expect($mailContent)
        ->toBeInstanceOf(MailContent::class);
    expect($mailContent->getSubject())
        ->toBe('Test');
    expect($mailContent->getHtml())
        ->toBe('<b>TEST</b>');
    expect($mailContent->getText())
        ->toBe('TEST');
    expect($mailContent->hasHtml())
        ->toBe(true);
});

it('can create an email without HTML', function () {
    $mailContent = new MailContent('Test', 'TEST');
    expect($mailContent)
        ->toBeInstanceOf(MailContent::class);
    expect($mailContent->getSubject())
        ->toBe('Test');
    expect($mailContent->getText())
        ->toBe('TEST');
    expect($mailContent->hasHtml())
        ->toBe(false);
});
