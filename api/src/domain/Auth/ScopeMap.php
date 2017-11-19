<?php
namespace Domain\Auth;

use Analogue\ORM\EntityMap;

/**
 * @inheritdoc
 */
class ScopeMap extends EntityMap
{
    protected $table = 'oauth_scopes';

    public $timestamps = true;
}
