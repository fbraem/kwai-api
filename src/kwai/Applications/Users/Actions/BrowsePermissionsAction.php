<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Actions;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Security\Rule;
use Kwai\Core\Infrastructure\Security\RuleResource;
use Kwai\Modules\Users\Domain\UserEntity;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Kwai\JSONAPI;

/**
 * Class BrowsePermissionsAction
 */
#[Route(
    path: '/users/permissions',
    name: 'users.permissions',
    options: ['auth' => true],
    methods: ['GET']
)]
final class BrowsePermissionsAction extends Action
{
    public function __construct(
        private ?Connection $database = null,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($logger);
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        /** @var UserEntity $user */
        $user = $request->getAttribute('kwai.user');
        if ($user->isAdmin()) {
            $ruleResource = new RuleResource(
                $user->getUuid(),
                collect([
                    new Rule(
                        subject: 'user_invitations',
                        action: 'manage'
                    ),
                    new Rule(
                        subject: 'users',
                        action: 'manage'
                    )
                ])
            );
        } else {
            $conditions = (object) [
                'id' => (string) $user->getUuid()
            ];
            $ruleResource = new RuleResource(
                $user->getUuid(),
                collect([
                    new Rule(
                        subject: 'users',
                        action: 'view',
                        conditions: $conditions
                    ),
                    new Rule(
                        subject: 'users',
                        action: 'update',
                        conditions: $conditions
                    )
                ])
            );
        }
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($ruleResource)
        ))($response);
    }
}
