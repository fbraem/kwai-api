<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Domain;

use Kwai\Modules\Users\Domain\ValueObjects\Username;

it('can create a username', function () {
    $username = new Username('Jigoro', 'Kano');
    expect(strval($username))
        ->toBe('Jigoro Kano')
    ;
});

it('can create an empty username', function () {
    $username = new Username();
    expect(strval($username))
        ->toBe('')
    ;
});
