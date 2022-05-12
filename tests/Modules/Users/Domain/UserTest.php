<?php
declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Modules\Users\Domain\User;

it('can find a permission', function () {
    $user = new User(
        uuid: new UniqueId(),
        emailAddress: new EmailAddress('jigoro.kano@kwai.com'),
        username: new Name('Jigoro', 'Kano'),
    );

    expect($user->isAdmin())->toBeFalse();
});
