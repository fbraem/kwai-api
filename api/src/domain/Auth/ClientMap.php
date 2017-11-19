<?php
namespace Domain\Auth;

use Analogue\ORM\EntityMap;

/**
 * @inheritdoc
 */
class ClientMap extends EntityMap
{
    protected $table = 'oauth_clients';

    public $timestamps = true;
}
