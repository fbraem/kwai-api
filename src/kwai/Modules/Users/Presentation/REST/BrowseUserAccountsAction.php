<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\REST;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Presentation\Resources\UserAccountResource;
use Kwai\Modules\Users\UseCases\BrowseUserAccounts;
use Kwai\Modules\Users\UseCases\BrowseUserAccountsCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

/**
 * Class BrowseUserAccountsAction
 */
class BrowseUserAccountsAction extends Action
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
    public function __invoke(Request $request, Response $response, array $args)
    {
        $repo = new UserAccountDatabaseRepository($this->database);
        $parameters = $request->getAttribute('parameters');

        $command = new BrowseUserAccountsCommand();
        $command->limit = (int) ($parameters['page']['limit'] ?? 10);
        $command->offset = (int) ($parameters['page']['offset'] ?? 0);

        try {
            [$count, $accounts] = BrowseUserAccounts::create($repo)($command);
        } catch (QueryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A query exception occurred.')
            )($response);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        }

        $resources = $accounts->map(
            fn ($account) => new UserAccountResource(
                $account,
                $request->getAttribute('kwai.user')
            )
        );
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromArray($resources->toArray())
                ->setMeta('count', $count)
        ))($response);
    }
}
