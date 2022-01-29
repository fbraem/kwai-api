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
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\Exceptions\RoleNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\RoleDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Kwai\Modules\Users\Presentation\Resources\RoleResource;
use Kwai\Modules\Users\UseCases\UpdateRole;
use Kwai\Modules\Users\UseCases\UpdateRoleCommand;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class UpdateRoleAction
 *
 * Action to update an existing role
 */
class UpdateRoleAction extends Action
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
                'id' => Expect::string(),
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
     * @return UpdateRoleCommand
     */
    private function createCommand(array $data): UpdateRoleCommand
    {
        $normalized = $this->processInput($data);

        $command = new UpdateRoleCommand();
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
        $roleRepo = new RoleDatabaseRepository($this->database);
        $ruleRepo = new RuleDatabaseRepository($this->database);

        try {
            $command = $this->createCommand($request->getParsedBody());
            $command->id = intval($args['id']);
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        try {
            $role = UpdateRole::create(
                $roleRepo,
                $ruleRepo
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (
                new SimpleResponse(500, 'A repository exception occurred.')
            )($response);
        } catch (RoleNotFoundException) {
            return (new NotFoundResponse('Role not found'))($response);
        }

        $resource = new RoleResource($role);

        return (new JSONAPIResponse(
            JSONAPI\Document::createFromObject($resource)
        ))($response);
    }
}
