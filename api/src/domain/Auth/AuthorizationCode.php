<?php
namespace Domain\Auth;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;

/**
 * @inheritdoc
 */
class AuthorizationCode implements AuthorizationCodeInterface, AuthCodeEntityInterface
{
    private $db;

    private $id;
    private $user;
    private $client;
    private $expiration;
    private $redirect_uri;
    private $revoked;
    private $scopes;

    use TokenTrait;
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
            'expiration' => $this->expiration(),
            'redirect_uri' => $this->redirectUri(),
            'revoked' => $this->revoked(),
            'created_at' => $this->createdAt(),
            'updated_at' => $this->updatedAt()
        ];
    }

    public function hydrate(iterable $data)
    {
        $this->id = $data['id'];
        $this->expiration = $data['expiration'];
        $this->redirect_uri = $data['redirect_uri'] ?? null;
        $this->revoked = $data['revoked'] ?? false;
        $this->client = $data['client'] ?? null;
        $this->user = $data['user'] ?? null;
        $this->scopes = $data['scopes'] ?? [];
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
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

    public function user()
    {
        return $this->user;
    }

    public function client()
    {
        return $this->client;
    }

    public function redirectUri()
    {
        return $this->redirect_uri;
    }

    public function revoked()
    {
        return $this->revoked;
    }

    public function scopes()
    {
        return $this->scopes;
    }

    public function store()
    {
        $table = new \Zend\Db\TableGateway\TableGateway('oauth_auth_codes', $this->db);
        $authCodeScopeTable = new \Zend\Db\TableGateway\TableGateway('oauth_auth_code_scopes', $this->db);

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

            if ($this->scopes) {
                $newScopes = array_map(function ($scope) {
                    return $scope->id;
                }, $this->scopes);

                $select = $authCodeScopeTable->select();
                $select->columns(['scope_id']);
                $select->where(['auth_code_id' => $this->id]);
                $resultSet = $authCodeScopeTable->selectWith($select);
                $originalScopes = [];
                foreach ($resultSet as $row) {
                    $originalScopes[] = $row->scope_id;
                }

                $toDelete = array_diff($originalScopes, $newScopes);
                $authCodeScopeTable->delete([
                    'auth_code_id' => $this->id,
                    'scope_id' => $toDelete
                ]);

                $toInsert = array_diff($newScopes, $originalScopes);
                $this->insertAuthCodeScopes($toInsert);
            }
        } else {
            $table->insert($data);
            $this->id = $table->getLastInsertValue();

            if ($this->scopes && count($this->scopes) > 0) {
                $id = $this->id;
                $authCodeScopes = array_map(function ($scope) use ($id) {
                    return [
                        'auth_code_id' => $id,
                        'scope_id' => $scope->id
                    ];
                }, $this->scopes);
                $authCodeScopeTable->insert($authCodeScopes);
            }
        }
    }

    private function insertAuthCodeScopes($scopeIds)
    {
        $authCodeScopeTable = new \Zend\Db\TableGateway\TableGateway('oauth_auth_code_scopes', $this->db);

        $id = $this->id;
        $authCodeScopes = array_map(function ($scope) use ($id) {
            return [
                'auth_code_id' => $id,
                'scope_id' => $scope
            ];
        }, $scopeIds);
        $authCodeScopeTable->insert($authCodeScopes);
    }
}
