<?php
namespace Domain\User;

use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * @inheritdoc
 */
class User implements UserInterface, UserEntityInterface
{
    private $id;
    private $email;
    private $password;
    private $last_login;
    private $first_name;
    private $last_name;
    private $remark;

    use \Domain\DatetimeMetaTrait;

    public function __construct($db, ?iterable $data)
    {
        $this->db = $db;
        if ($data) {
            $this->hydrate($data);
        }
    }

    public function hydrate(iterable $data)
    {
        $this->id = $data['id'] ?? null;
        $this->email = $data['email'];
        $this->password = $data['password'] ?? null;
        $this->last_login = $data['last_login'] ?? null;
        $this->first_name = $data['first_name'] ?? null;
        $this->last_name = $data['last_name'] ?? null;
        $this->remark = $data['remark'] ?? null;
    }

    public function extract() : iterable
    {
        return [
            'id' => $this->id(),
            'email' => $this->email(),
            'password' => $this->password(),
            'last_login' => $this->lastLogin(),
            'first_name' => $this->firstName(),
            'last_name' => $this->lastName(),
            'remark' => $this->remark(),
            'created_at' => $this->createdAt()
        ];
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('users', $this->db);
        if ($this->id()) {
            $this->updated_at = \Carbon\Carbon::now()->toDateTimeString();
            $table->update($this->extract(), $this->id());
        } else {
            $this->created_at = \Carbon\Carbon::now()->toDateTimeString();
            $table->insert($this->extract());
            $this->id = $table->getLastInsertValue();
        }
    }

    public function verify(string $password) : bool
    {
        if (password_verify($password, $this->password())) {
            $this->last_login = \Carbon\Carbon::now();
            $this->store();
            return true;
        }
        return false;
    }

    public function getIdentifier()
    {
        return $this->id();
    }

    public function id() : ?int
    {
        return $this->id;
    }

    public function email() : string
    {
        return $this->email;
    }

    public function password() : string
    {
        return $this->password;
    }

    public function lastLogin() : ?string
    {
        return $this->last_login;
    }

    public function firstName() : ?string
    {
        return $this->first_name;
    }

    public function lastName() : ?string
    {
        return $this->last_name;
    }

    public function remark() : ?string
    {
        return $this->remark;
    }
}
