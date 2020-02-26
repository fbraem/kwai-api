<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;
use PHPUnit\Framework\TestCase;

final class MailContentTest extends TestCase
{
    public function testMailContentWithHtml(): void
    {
        $mailContent = new MailContent('Test', 'TEST', '<b>TEST</b>');
        $this->assertInstanceOf(
            MailContent::class,
            $mailContent
        );
        $this->assertEquals('Test', $mailContent->getSubject());
        $this->assertEquals('<b>TEST</b>', $mailContent->getHtml());
        $this->assertEquals('TEST', $mailContent->getText());
        $this->assertTrue($mailContent->hasHtml());
    }

    public function testMailContentWithoutHtml(): void
    {
        $mailContent = new MailContent('Test', 'TEST');
        $this->assertInstanceOf(
            MailContent::class,
            $mailContent
        );
        $this->assertEquals('Test', $mailContent->getSubject());
        $this->assertEquals('', $mailContent->getHtml());
        $this->assertEquals('TEST', $mailContent->getText());
        $this->assertFalse($mailContent->hasHtml());
    }
}
