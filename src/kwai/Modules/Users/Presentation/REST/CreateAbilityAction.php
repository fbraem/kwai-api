<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\REST;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Dependencies\DatabaseDependency;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONAPIResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Kwai\Modules\Users\Presentation\Resources\AbilityResource;
use Kwai\Modules\Users\UseCases\CreateAbility;
use Kwai\Modules\Users\UseCases\CreateAbilityCommand;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class CreateAbilityAction
 *
 * Action to create a new ability
 */
class CreateAbilityAction extends Action
{
    public function __construct(
        private ?Connection $database = null
    ) {
        parent::__construct();
        $this->database ??= depends('kwai.database', DatabaseDependency::class);
    }

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
     * @return CreateAbilityCommand
     */
    private function createCommand(array $data): CreateAbilityCommand
    {
        $normalized = $this->processInput($data);

        $command = new CreateAbilityCommand();
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
        $abilityRepo = new AbilityDatabaseRepository($this->database);
        $ruleRepo = new RuleDatabaseRepository($this->database);

        try {
            $command = $this->createCommand($request->getParsedBody());
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        try {
            $ability = (new CreateAbility(
                $abilityRepo,
                $ruleRepo
            ))($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        }
        $resource = new AbilityResource($ability);

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);    }
}
