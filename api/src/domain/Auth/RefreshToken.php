<?php
namespace Domain\Auth;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;

/**
 * @inheritdoc
 */
class RefreshToken implements RefreshTokenInterface, RefreshTokenEntityInterface
{
    private $db;

    private $id;
    private $accessToken;
    private $expiration;
    private $revoked = false;

    use IdentifierTrait;
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
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        ];
    }

    public function hydrate(iterable $data)
    {
        $this->id = $data['id'];
        $this->identifier = $data['identifier'];
        $this->accessToken = $data['access_token'] ?? null;
        $this->expiration = $data['expiration'];
        $this->revoked = $data['revoked'] ?? false;
        $this->created_at = $data['created_at'];
        $this->updated_at = $data['updated_at'];
    }

    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getExpiryDateTime()
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->expiration);
    }

    public function setExpiryDateTime(\DateTime $datetime)
    {
        $this->expiration = \Carbon\Carbon::instance($datetime)->toDateTimeString();
    }

    public function id() : ?int
    {
        return $this->id;
    }

    public function accessToken()
    {
        return $this->accessToken;
    }

    public function expiration()
    {
        return $this->expiration;
    }

    public function revoked()
    {
        return $this->revoked;
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('oauth_refresh_tokens', $this->db);

        if ($this->id()) {
            $this->created_at = \Carbon\Carbon::now()->toDateTimeString();
        } else {
            $this->updated_at = \Carbon\Carbon::now()->toDateTimeString();
        }

        $data = $this->extract();
        if ($this->accessToken) {
            $data['access_token_id'] = $this->accessToken->id();
        }

        if ($this->id()) {
            $table->update($data, ['id' => $this->id()]);
        } else {
            $table->insert($data);
            $this->id = $table->getLastInsertValue();
        }
    }

    public function revoke()
    {
        $this->revoked = true;
    }
}
