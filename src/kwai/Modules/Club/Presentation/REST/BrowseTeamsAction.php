<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Presentation\REST;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\JSONAPI;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Club\Infrastructure\Repositories\TeamDatabaseRepository;
use Kwai\Modules\Club\Presentation\Resources\TeamResource;
use Kwai\Modules\Club\UseCases\BrowseTeams;
use Kwai\Modules\Club\UseCases\BrowseTeamsCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseTeamsAction
 */
class BrowseTeamsAction extends Action
{
    public function __construct(
        private ?Connection $database = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $repo = new TeamDatabaseRepository($this->database);
        $command = new BrowseTeamsCommand();

        try {
            [$count, $teams] = BrowseTeams::create($repo)($command);
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

        $resources = $teams->map(
            fn ($team) => new TeamResource($team)
        );
        $meta = [
            'count' => $count
        ];
        if ($command->limit) {
            $meta['limit'] = $command->limit;
            $meta['offset'] = $command->offset ?? 0;
        }
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromArray($resources->toArray())
                ->setMeta($meta)
        ))($response);
    }
}
