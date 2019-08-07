<?php

namespace REST\Users\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\ORM\Entity;
use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\User\UsersTable;
use Domain\User\AbilitiesTable;
use Domain\User\AbilityTransformer;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;

class UserAttachAbilityAction
{
    private $container;

    private $inputValidator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $abilitiesTable = AbilitiesTable::getTableFromRegistry();
        $usersTable = UsersTable::getTableFromRegistry();

        try {
            $ability = $abilitiesTable->get(
                $args['ability'],
                [
                    'contain' => [
                        'Rules',
                        'Rules.RuleAction',
                        'Rules.RuleSubject'
                    ]
                ]
            );
        } catch (RecordNotFoundException $rnfe) {
            return (new NotFoundResponse(_("Ability doesn't exist.")))($response);
        }

        try {
            $user = $usersTable->get(
                $args['id'],
                [
                    'contain' => [
                        'Abilities'
                    ]
                ]
            );
            $user->abilities[] = $ability;
            $user->setDirty('abilities', true);
            $usersTable->save($user);
        } catch (RecordNotFoundException $rnfe) {
            return (new NotFoundResponse(_("User doesn't exist.")))($response);
        }

        $route = $request->getAttribute('route');
        if (! empty($route)) {
            $route->setArgument('id', $user->id);
        }

        return (new ResourceResponse(
            AbilityTransformer::createForCollection($user->abilities)
        ))($response);
    }
}
