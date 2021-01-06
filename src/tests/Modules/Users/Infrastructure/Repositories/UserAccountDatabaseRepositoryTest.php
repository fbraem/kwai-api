<?php
declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get a user account', function () use ($context) {
    $repo = new UserAccountDatabaseRepository($context->db);
    try {
        $repo->get(new EmailAddress('jigoro.kano@kwai.com'));
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
