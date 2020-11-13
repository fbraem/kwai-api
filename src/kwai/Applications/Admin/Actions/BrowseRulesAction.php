<?php
/**
 * @package Applications
 * @subpackage Admin
 */
declare(strict_types=1);

namespace Kwai\Applications\Admin\Actions;

use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\RuleTransformer;
use Kwai\Modules\Users\UseCases\BrowseRules;
use Kwai\Modules\Users\UseCases\BrowseRulesCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * BrowseRulesAction
 */
class BrowseRulesAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new BrowseRulesCommand();

        $parameters = $request->getAttribute('parameters');
        if (array_key_exists('subject', $parameters['filter'])) {
            $command->subject = $parameters['filter']['subject'];
        }

        try {
            $rules = (new BrowseRules(
                new RuleDatabaseRepository($this->getContainerEntry('pdo_db'))
            ))($command);
            return (new ResourceResponse(
                RuleTransformer::createForCollection($rules)
            ))($response);
        } catch (RepositoryException $e) {
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        }
    }
}
