<?php
/**
 * AccessToken Repository.
 * @package kwai
 * @subpackage Auth
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Exceptions\NotCreatedException;
use Kwai\Core\Infrastructure\TableData;
use Kwai\Core\Infrastructure\Database;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\Mappers\AccessTokenMapper;
use Kwai\Modules\Users\Infrastructure\AccessTokenTable;

use Kwai\Modules\Users\Repositories\AccessTokenRepository;

use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\alias;

/**
* AccessToken Repository for read/write AccessToken entity from/to a database.
*/
final class AccessTokenDatabaseRepository implements AccessTokenRepository
{
    /**
     * @var Database
     */
    private $db;

    /**
     * AccessToken table
     * @var AccessTokenTable
     */
    private $table;

    /**
     * Constructor
     *
     * @param Database $db A database object
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->table = new AccessTokenTable();
    }

    private function getColumns(): array
    {
        return [
            alias('oauth_access_tokens.id', 'oauth_access_tokens_id'),
            alias('oauth_access_tokens.identifier', 'oauth_access_tokens_identifier'),
            alias('oauth_access_tokens.expiration', 'oauth_access_tokens_expiration'),
            alias('oauth_access_tokens.revoked', 'oauth_access_tokens_revoked'),
            alias('oauth_access_tokens.created_at', 'oauth_access_tokens_created_at'),
            alias('oauth_access_tokens.updated_at', 'oauth_access_tokens_updated_at')
        ];
    }

    /**
     * Get an accesstoken by its token identifier.
     *
     * @param  TokenIdentifier $identifier A token identifier
     * @return Entity<AccessToken>         An accesstoken
     */
    public function getByTokenIdentifier(TokenIdentifier $identifier) : Entity
    {
        $query = $this->db->createQueryFactory()
            ->select(... $this->getColumns())
            ->from('oauth_access_tokens')
            ->where(field('identifier')->eq(strval($identifier)))
            ->compile();

        $row = $this->db->execute($query)->fetch();
        if ($row) {
            return AccessTokenMapper::toDomain(
                new TableData($row, 'oauth_access_tokens_')
            );
        }
        throw new NotFoundException('AccessToken');
    }

    /**
     * Get all accesstokens of a user.
     * @param  Entity<User> $user
     * @return Entity<AccessToken>[]  An array with accesstokens
     */
    public function getTokensForUser(Entity $user): array
    {
        $query = $this->db->createQueryFactory()
            ->select(... $this->getColumns())
            ->from('oauth_access_tokens')
            ->where(field('user_id')->eq($user->id()))
            ->compile()
        ;

        $rows = $this->db->execute($query)->fetchAll();

        $tokens = [];
        foreach ($rows as $row) {
            $token = AccessTokenMapper::toDomain(
                new TableData($row, 'oauth_access_tokens_')
            );
            $token->attachUser($user);
            $tokens[] = $token;
        }
        return $tokens;
    }

    /**
     * Inserts the accesstoken in the table.
     * @param  AccessToken $token
     * @return Entity<User>
     * @throws NotCreatedException Thrown when insert fails
     */
    public function create(AccessToken $token): Entity
    {
        $data = AccessTokenMapper::toPersistence($token);

        $query = $this->db->createQueryFactory()
            ->insert('oauth_access_tokens')
            ->columns(
                'identifier',
                'expiration',
                'revoked',
                'created_at',
                'updated_at'
            )
            ->values(
                $data->identifier,
                $data->expiration,
                $data->revoked,
                $data->created_at,
                $data->updated_at
            )
            ->compile()
        ;
        $stmt = $this->db->execute($query);

        return new Entity(
            $this->db->lastInsertId(),
            $token
        );
    }
}
