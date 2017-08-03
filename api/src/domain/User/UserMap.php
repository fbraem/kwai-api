<?php
namespace Domain\User;

use Analogue\ORM\EntityMap;

/**
 * @inheritdoc
 */
class UserMap extends EntityMap
{
    protected $table = 'users';

    public $timestamps = true;
}
