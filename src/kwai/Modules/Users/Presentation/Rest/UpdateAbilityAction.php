<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\Rest;

use Kwai\Core\Infrastructure\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Responses\SimpleResponse;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Kwai\Modules\Users\Presentation\Transformers\AbilityTransformer;
use Kwai\Modules\Users\UseCases\UpdateAbility;
use Kwai\Modules\Users\UseCases\UpdateAbilityCommand;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class UpdateAbilityAction
 *
 * Action to update an existing ability
 */
class UpdateAbilityAction extends Action
{
    /**
     * Create the JSONAPI schema for this action
     *
     * @return Structure
     */
    private function createSchema(): Structure
    {
        return Expect::structure([
            'data' => Expect::structure([
                'type' => Expect::string(),
                'attributes' => Expect::structure([
                    'name' => Expect::string()->required(),
                    'remark' => Expect::string()
                ]),
                'relationships' => Expect::structure([
                    'rules' => Expect::structure([
                        'data' => Expect::arrayOf(
                            Expect::structure([
                                'type' => Expect::string(),
                                'id' => Expect::string()
                                ])
                        )
                    ])
                ])
            ])
        ]);
    }

    /**
     * Process the data
     *
     * @param $data
     * @return mixed
     */
    private function processInput($data)
    {
        $processor = new Processor();
        return $processor->process($this->createSchema(), $data);
    }

    /**
     * Creates a command from the input data
     *
     * @param array $data
     * @return UpdateAbilityCommand
     */
    private function createCommand(array $data): UpdateAbilityCommand
    {
        $normalized = $this->processInput($data);

        $command = new UpdateAbilityCommand();
        $command->name = $normalized->data->attributes->name;
        $command->remark = $normalized->data->attributes->remark;
        foreach ($normalized->data->relationships->rules->data as $rule) {
            $command->rules[] = $rule->id;
        }

        return $command;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $database = $this->getContainerEntry('pdo_db');
        $abilityRepo = new AbilityDatabaseRepository($database);
        $ruleRepo = new RuleDatabaseRepository($database);

        try {
            $command = $this->createCommand($request->getParsedBody());
            $command->id = intval($args['id']);
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        try {
            $ability = (new UpdateAbility(
                $abilityRepo,
                $ruleRepo
            ))($command);
        } catch (RepositoryException $e) {
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (NotFoundException $e) {
            return (new NotFoundResponse('Ability not found'))($response);
        }

        return (new ResourceResponse(
            AbilityTransformer::createForItem(
                $ability
            )
        ))($response);
    }
}
