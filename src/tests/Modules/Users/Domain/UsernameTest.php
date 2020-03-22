<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Domain;

use Kwai\Modules\Users\Domain\ValueObjects\Username;
use PHPUnit\Framework\TestCase;

final class UsernameTest extends TestCase
{
    public function testToString(): void
    {
        $username = new Username('Jigoro', 'Kano');
        $this->assertEquals('Jigoro Kano', strval($username));
    }

    public function testEmpty(): void
    {
        $username = new Username();
        $this->assertEquals('', strval($username));
    }
}
