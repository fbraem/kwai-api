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
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Kwai\Modules\Users\Presentation\Resources\RuleResource;
use Kwai\Modules\Users\UseCases\BrowseRules;
use Kwai\Modules\Users\UseCases\BrowseRulesCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * BrowseRulesAction
 */
class BrowseRulesAction extends Action
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
        $command = new BrowseRulesCommand();

        $parameters = $request->getAttribute('parameters');
        $command->subject = $parameters['filter']['subject'] ?? null;

        try {
            [$count, $rules] = BrowseRules::create(
                new RuleDatabaseRepository($this->database)
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (QueryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A query exception occurred.')
            )($response);
        }

        $resources = $rules->map(fn ($rule) => new RuleResource($rule));
        return (new JSONAPIResponse(
            JSONAPI\Document::createFromArray($resources->toArray())
                ->setMeta('count', $count)
        ))($response);
    }
}
