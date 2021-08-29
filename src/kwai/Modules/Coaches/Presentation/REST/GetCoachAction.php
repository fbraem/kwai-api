<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Presentation\REST;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Coaches\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Coaches\Infrastructure\Repositories\CoachDatabaseRepository;
use Kwai\Modules\Coaches\Presentation\Transformers\CoachTransformer;
use Kwai\Modules\Coaches\UseCases\GetCoach;
use Kwai\Modules\Coaches\UseCases\GetCoachCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class GetCoachAction
 */
class GetCoachAction extends Action
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
        $command = new GetCoachCommand();
        $command->id = (int) $args['id'];

        try {
            $coach = GetCoach::create(new CoachDatabaseRepository($this->database))($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);

        } catch (CoachNotFoundException) {
            return (new NotFoundResponse('Coach not found'))($response);
        }

        $user = $request->getAttribute('kwai.user');
        $resource = CoachTransformer::createForItem($coach, $user);

        return (new ResourceResponse($resource))($response);
    }
}
