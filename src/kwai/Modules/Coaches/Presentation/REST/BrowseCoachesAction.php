<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Presentation\REST;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Coaches\Infrastructure\Repositories\CoachDatabaseRepository;
use Kwai\Modules\Coaches\Presentation\Resources\CoachResource;
use Kwai\Modules\Coaches\UseCases\BrowseCoaches;
use Kwai\Modules\Coaches\UseCases\BrowseCoachesCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseCoachesAction
 */
class BrowseCoachesAction extends Action
{
    public function __construct(
        private ?Connection $database = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $repo = new CoachDatabaseRepository($this->database);
        $command = new BrowseCoachesCommand();

        $parameters = $request->getAttribute('parameters');

        if (isset($parameters['filter']['active'])) {
            $command->active = (bool) $parameters['filter']['active'];
        }
        //TODO: check if this is allowed.
        /*
        if (!$command->active) {
        }
        */

        $command->limit = $parameters['page']['limit'] ?? null;
        $command->offset = $parameters['page']['offset'] ?? null;

        try {
            [$count, $coaches] = BrowseCoaches::create($repo)($command);
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

        $user = $request->getAttribute('kwai.user');
        $resources = $coaches->map(
            fn ($coach) => new CoachResource($coach, $user)
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
