<?php
/**
 * AccessToken Repository.
 * @package kwai
 * @subpackage Auth
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

use Kwai\Modules\Users\Infrastructure\Mappers\AccessTokenMapper;

use Kwai\Modules\Users\Repositories\AccessTokenRepository;

use Opis\Database\Database;

/**
* AccessToken Repository for read/write AccessToken entity from/to a database.
*/
final class AccessTokenDatabaseRepository implements AccessTokenRepository
{
    const TABLE_NAME = 'oauth_access_tokens';

    /**
     * @var Database
     */
    private $db;

    /**
     * Constructor
     *
     * @param Database $db A database object
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Get an accesstoken by its token identifier.
     *
     * @param  TokenIdentifier $identifier A token identifier
     * @return AccessToken                 An accesstoken
     */
    public function getByTokenIdentifier(TokenIdentifier $identifier) : AccessToken
    {
        $row = $this->db->from(self::TABLE_NAME)
            ->where('identifier')->is(strval($identifier))
            ->select()
            ->first();
        if ($row) {
            return AccessTokenMapper::toDomain($row);
        }
        throw new NotFoundException('AccessToken');
    }

    /**
     * Get all accesstokens of a user.
     * @param  int     $user_id A user id
     * @return array            An array with accesstokens
     */
    public function getTokensForUser(User $user): array
    {
        $rows = $this->db->from(self::TABLE_NAME)
            ->where('user_id')->is($user->id())
            ->select()
            ->all();
        ;
        if ($rows) {
            $tokens = [];
            foreach ($rows as $row) {
                $tokens[] = AccessTokenMapper::toDomain($row);
            }
            return $tokens;
        }
        throw new NotFoundException('User');
    }
}
