<?php

namespace REST\Teams\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\Team\TeamMembersTable;

//TODO: Remove sport dependency?
use Judo\Domain\Member\MembersTable;
use Judo\Domain\Member\MemberTransformer;

use Core\Responses\ResourceResponse;

class TeamAvailableMembersBrowseAction
{
    private static $_logicExpressions = [
        '>' => 'gt',
        '<' => 'lt',
        '>=' => 'gte',
        '<=' => 'lte',
        '!=' => 'notEq',
        '=' => 'eq'
    ];

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $teamTable = \Domain\Team\TeamsTable::getTableFromRegistry();
        try {
            $team = $teamTable->get($args['id'], [
                'contain' => ['TeamType', 'Season']
            ]);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Team doesn't exist"));
        }

        $membersTable = MembersTable::getTableFromRegistry();
        $query =$membersTable
            ->find()
            ->contain('Person')
        ;

        $members = TeamMembersTable::getTableFromRegistry()
            ->find()
            ->select(['member_id'])
            ->where(['team_id' => $team->id])
        ;

        $query->where([$membersTable->getAlias() . '.id NOT IN' => $members]);

        if ($team->team_type) {
            if ($team->team_type->gender) {
                $query->where(['Person.gender' => $team->team_type->gender]);
            }
            if ($team->season) {
                if ($team->team_type->start_age) {
                    $query->where(function (QueryExpression $exp, Query $q) use ($team) {
                        return $exp->gte("TIMESTAMPDIFF(YEAR, Person.birthdate, '" . $team->season->end_date . "')", $team->team_type->start_age);
                    });
                }
                if ($team->team_type->end_age) {
                    $query->where(function (QueryExpression $exp, Query $q) use ($team) {
                        return $exp->lte("TIMESTAMPDIFF(YEAR, Person.birthdate, '" . $team->season->end_date . "')", $team->team_type->end_age);
                    });
                }
            } else {
                $endOfYearDate = \Carbon\Carbon::now()->endOfYear();
                if ($team->team_type->start_age) {
                    $query->where(function (QueryExpression $exp, Query $q) use ($team, $endOfYearDate) {
                        return $exp->gte("TIMESTAMPDIFF(YEAR, Person.birthdate, '" . $endOfYearDate . "')", $team->team_type->start_age);
                    });
                }
                if ($team->team_type->end_age) {
                    $query->where(function (QueryExpression $exp, Query $q) use ($team, $endOfYearDate) {
                        return $exp->lte("TIMESTAMPDIFF(YEAR, Person.birthdate, '" . $endOfYearDate . "')", $team->team_type->end_age);
                    });
                }
            }
        } else {
            $parameters = $request->getAttribute('parameters');
            if (isset($parameters['filter']['start_age'])) {
                if (preg_match("/([><!]?=?)?\s*([0-9]*)/", $parameters['filter']['start_age'], $matches)) {
                    $operator = $matches[1];
                    if ($operator == null) {
                        $operator = '=';
                    }
                    $method = self::$_logicExpressions[$operator] ?? 'eq';
                    $age = $matches[2];
                    $query->where(function (QueryExpression $exp, Query $q) use ($method, $age) {
                        $endOfYearDate = \Carbon\Carbon::now()->endOfYear();
                        $condition = "TIMESTAMPDIFF(YEAR, Person.birthdate, '" . $endOfYearDate . "')";
                        return $exp->$method($condition, $age);
                    });
                }
            }
            if (isset($parameters['filter']['end_age'])) {
                if (preg_match("/([><!]?=?)?\s*([0-9]*)/", $parameters['filter']['end_age'], $matches)) {
                    $operator = $matches[1];
                    if ($operator == null) {
                        $operator = '=';
                    }
                    $method = self::$_logicExpressions[$operator] ?? 'eq';
                    $age = $matches[2];
                    $query->where(function (QueryExpression $exp, Query $q) use ($method, $age) {
                        $endOfYearDate = \Carbon\Carbon::now()->endOfYear();
                        $condition = "TIMESTAMPDIFF(YEAR, Person.birthdate, '" . $endOfYearDate . "')";
                        return $exp->$method($condition, $age);
                    });
                }
            }
            if (isset($parameters['filter']['gender'])) {
                $query->where(['Person.gender' => $parameters['filter']['gender']]);
            }
        }

        $members = $query->all();

        //TODO: Remove sport dependency?
        return (new ResourceResponse(
            MemberTransformer::createForCollection($members)
        ))($response);
    }
}
