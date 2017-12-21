<?php
namespace Domain\Auth;

use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * @inheritdoc
 */
class Client implements ClientInterface, ClientEntityInterface
{
    private $db;
    use IdentifierTrait;
    use \Domain\DatetimeMetaTrait;

    private $id;
    private $name;
    private $secret;
    private $redirectUri;
    private $status;

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
        $this->name = $data['name'];
        $this->identifier = $data['identifier'];
        $this->secret = $data['secret'];
        $this->redirectUri = $data['redirect_uri'];
        $this->status = $data['status'] ?? 1;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('oauth_access_clients', $this->db);

        $data = $this->extract();

        if ($this->id) {
            $this->created_at = \Carbon\Carbon::now()->toDateTimeString();
            $table->insert($data);
            $this->id = $table->getLastInsertValue();
        } else {
            $this->updated_at = \Carbon\Carbon::now()->toDateTimeString();
            $table->update($data, ['id' => $this->id]);
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }

    public function setRedirectUri($uri)
    {
        $this->redirect_uri = $uri;
    }

    public function id() : ?int
    {
        return $this->id;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function secret() : string
    {
        return $this->secret;
    }

    public function redirectUri() : string
    {
        return $this->redirectUri;
    }

    public function status() : int
    {
        return $this->status;
    }
}
