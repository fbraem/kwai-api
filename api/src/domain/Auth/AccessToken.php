<?php
namespace Domain\Auth;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\EntityInterface;

class AccessToken implements AccessTokenEntityInterface, AccessTokenInterface, \Domain\HydratorInterface
{
    private $db;

    private $id;
    private $client;
    private $user;
    private $expiration;
    private $revoked;
    private $type;
    private $scopes;

    use AccessTokenTrait;
    use IdentifierTrait;
    use TokenTrait;

    use \Domain\DatetimeMetaTrait;

    public function __construct($db, ?iterable $data = null)
    {
        $this->db = $db;
        if ($data) {
            $this->hydrate($data);
        }
    }

    public function extract() : iterable
    {
        return [
            'id' => $this->id(),
            'identifier' => $this->identifier(),
            'expiration' => $this->expiration(),
            'revoked' => $this->revoked(),
            'type' => $this->type(),
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        ];
    }

    public function hydrate(iterable $data)
    {
        $this->id = $data['id'] ?? null;
        $this->identifier = $data['identifier'] ?? null;
        $this->client = $data['client'] ?? null;
        $this->user = $data['user'] ?? null;
        $this->expiration = $data['expiration'] ?? null;
        $this->revoked = $data['revoked'] ?? false;
        $this->type = $data['type'] ?? 1;
        $this->scopes = $data['scopes'] ?? [];
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('oauth_access_tokens', $this->db);

        if ($this->id) {
            $this->updated_at = \Carbon\Carbon::now()->toDateTimeString();
        } else {
            $this->created_at = \Carbon\Carbon::now()->toDateTimeString();
        }

        $data = $this->extract();
        if ($this->client) {
            $data['client_id'] = $this->client->id();
        }
        if ($this->user) {
            $data['user_id'] = $this->user->id();
        }
        if ($this->id) {
            $table->update($data, ['id' => $this->id]);
        } else {
            $table->insert($data);
            $this->id = $table->getLastInsertValue();
        }
    }

    public function revoke()
    {
        $this->revoked = true;
    }

    public function id() : ?int
    {
        return $this->id;
    }

    public function client()
    {
        return $this->client;
    }

    public function user()
    {
        return $this->user;
    }

    public function expiration()
    {
        return $this->expiration;
    }

    public function revoked()
    {
        return $this->revoked;
    }

    public function type()
    {
        return $this->type;
    }

    public function scopes()
    {
        return $this->scopes;
    }
}
