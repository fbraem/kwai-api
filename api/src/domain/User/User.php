<?php
namespace Domain\User;

use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * @inheritdoc
 */
class User extends \Cake\ORM\Entity implements UserEntityInterface
{
    protected $_hidden = ['password'];

    use \Domain\DatetimeMetaTrait;

    public function verify(string $password) : bool
    {
        if (password_verify($password, $this->password)) {
            $this->last_login = \Carbon\Carbon::now();
            $table = UsersTable::getTableFromRegistry();
            $table->save($this);
            return true;
        }
        return false;
    }

    public function getIdentifier()
    {
        return $this->id;
    }

    public function _getLastLogin($value)
    {
        if ($value) {
            return (new \Carbon\Carbon($value))->toDateTimeString();
        }
        return null;
    }
}
