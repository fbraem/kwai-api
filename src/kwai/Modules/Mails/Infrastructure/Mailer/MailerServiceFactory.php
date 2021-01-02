<?php
/**
 * @package Modules
 * @subpackage Mails
 */
declare(strict_types=1);

namespace Kwai\Modules\Mails\Infrastructure\Mailer;

use InvalidArgumentException;
use League\Uri\UriString;

/**
 * MailerServiceFactory
 */
class MailerServiceFactory
{
    /**
     * Creates a MailerService using a URI.
     * At the moment only smtp protocol is supported.
     * @param string $uri
     * @return MailerService
     */
    public function create(string $uri): MailerService
    {
        $uriParts = UriString::parse($uri);
        if ($uriParts['scheme'] == 'smtp') {
            return new SmtpMailerService(
                $uriParts['user'],
                $uriParts['pass'],
                $uriParts['host'],
                $uriParts['port'] ?? 25
            );
        }
        throw new InvalidArgumentException('Unknown protocol used: ' . $uriParts['scheme']);
    }
}
