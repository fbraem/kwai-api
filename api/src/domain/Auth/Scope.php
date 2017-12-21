<?php
namespace Domain\Auth;

use League\OAuth2\Server\Entities\ScopeEntityInterface;

class Scope implements ScopeInterface, ScopeEntityInterface
{
    private $db;

    private $id;

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
            'identifier' => $this->identifer(),
            'description' => $this->description(),
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        ];
    }

    public function hydrate(iterable $data)
    {
        $this->id = $data['id'];
        $this->identifier = $data['identifier'];
        $this->description = $data['description'];
        $this->created_at = $data['created_at'];
        $this->updated_at = $data['updated_at'];
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('oauth_scopes', $this->db);

        if ($this->id) {
            $this->updated_at = \Carbon\Carbon::now()->toDateTimeString();
        } else {
            $this->created_at = \Carbon\Carbon::now()->toDateTimeString();
        }

        $data = $this->extract();
        if ($this->id) {
            $table->update($data, ['id' => $this->id]);
        } else {
            $table->insert($data);
            $this->id = $table->getLastInsertValue();
        }
    }

    public function id() : ?int
    {
        return $this->id;
    }

    public function description() : string
    {
        return $this->description;
    }

    //TODO: do we need this?
    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
