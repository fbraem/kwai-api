<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Dependencies;

use Kwai\Modules\Mails\Infrastructure\Mailer\MailerServiceFactory;

/**
 * Class MailerDependency
 */
class MailerDependency implements Dependency
{
    public function __invoke(array $settings)
    {
        return (new MailerServiceFactory())->create(
            $settings['mail']['url']
        );
    }
}
