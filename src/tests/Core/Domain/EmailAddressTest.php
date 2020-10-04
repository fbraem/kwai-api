<?php
/**
 * Testcase for EmailAddress
 */
declare(strict_types=1);

namespace Tests\Core\Domain;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Kwai\Core\Domain\ValueObjects\EmailAddress;

it('can create an email address', function () {
    $emailAddress = new EmailAddress('user@example.com');
    expect((string) $emailAddress)->toBe('user@example.com');
});

it('can detect an invalid email address', function () {
    new EmailAddress('invalid');
})->throws(InvalidArgumentException::class);
