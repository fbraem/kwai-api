<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\TraceableTime;

use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use Kwai\Modules\Users\Domain\AccessToken;

/**
 * Usecase: Create a new accesstoken
 */
final class CreateAccessToken
{
    private $accessTokenRepo;

    /**
     * Constructor.
     * @param AccessTokenRepository $repo An accesstoken repository
     */
    public function __construct(AccessTokenRepository $repo)
    {
        $this->accessTokenRepo = $repo;
    }

    /**
     * Creates a new accesstoken.
     * @param  CreateAccessTokenCommand $command
     * @return Entity                             A new accesstoken
     */
    public function __invoke(CreateAccessTokenCommand $command): Entity
    {
        $token = new AccessToken((object)[
            'identifier' => $command->identifier,
            'expiration' => $command->expiration,
            'revoked' => false,
            'traceableTime' => new TraceableTime()
        ]);
        return $repo->create($accessToken);
    }
}
