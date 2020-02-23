<?php

namespace Kwai\Modules\Mails\Infrastructure;

use Kwai\Core\Infrastructure\Database\Table;

final class RecipientsTable extends Table
{
    public function __construct()
    {
        parent::__construct('mails_recipients', [
            'id',
            'mail_id',
            'type',
            'email',
            'name'
        ]);
    }
}
