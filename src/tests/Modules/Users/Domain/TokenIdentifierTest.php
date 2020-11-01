<?php
/**
 * Testcase for TokenIdentifier
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Domain;

use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

it('can create a new token identifier', function () {
    $identifier = new TokenIdentifier();
    expect($identifier)
        ->toBeInstanceOf(TokenIdentifier::class)
    ;
    preg_match_all('/[0-9a-fA-F]{80}/', strval($identifier), $matches);
    expect($matches[0][0])
        ->toBeString()
    ;
    expect(strlen($matches[0][0]))
        ->toBe(80)
    ;
});
