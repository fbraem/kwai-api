<?php

namespace Kwai\Modules\Mails\Infrastructure;

use Kwai\Core\Infrastructure\Database\Table;

final class MailsTable extends Table
{
    public function __construct()
    {
        parent::__construct('mails', [
            'id',
            'tag',
            'uuid',
            'sender_email',
            'sender_name',
            'subject',
            'html_body',
            'text_body',
            'sent_time',
            'remark',
            'user_id',
            'created_at',
            'updated_at'
        ]);
    }
}
