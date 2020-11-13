<?php

namespace REST\Teams\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\Team\TeamMembersTable;

//TODO: Remove sport dependency?
use Judo\Domain\Member\MembersTable;
use Judo\Domain\Member\MemberTransformer;

use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;

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
                'contain' => ['TeamCategory', 'Season']
            ]);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Team doesn't exist"));
        }

        $membersTable = MembersTable::getTableFromRegistry();
        $query = $membersTable
            ->find()
            ->contain('Person')
        ;

        $members = TeamMembersTable::getTableFromRegistry()
            ->find()
            ->select(['member_id'])
            ->where(['team_id' => $team->id])
        ;

        $query->where([$membersTable->getAlias() . '.id NOT IN' => $members]);

        if ($team->team_category) {
            if ($team->team_category->gender) {
                $query->where(['Person.gender' => $team->team_category->gender]);
            }
            // When the team is attached to a season, the age of a member is
            // calculated on the end date of the season.
            if ($team->season) {
                $diff = $query->func()->TIMESTAMPDIFF([
                    'YEAR' => 'literal',
                    'Person.birthdate' => 'identifier',
                    $team->season->end_date
                ]);
                if ($team->team_category->start_age) {
                    $query->where(function (QueryExpression $exp, Query $q) use ($team, $diff) {
                        return $exp->gte($diff, $team->team_category->start_age);
                    });
                }
                if ($team->team_category->end_age) {
                    $query->where(function (QueryExpression $exp, Query $q) use ($team, $diff) {
                        return $exp->lte($diff, $team->team_category->end_age);
                    });
                }
            } else {
                // Age is calculated on the end of the year
                $diff = $query->func()->TIMESTAMPDIFF([
                    'YEAR' => 'literal',
                    'Person.birthdate' => 'identifier',
                    \Carbon\Carbon::now()->endOfYear()
                ]);
                if ($team->team_category->start_age) {
                    $query->where(function (QueryExpression $exp, Query $q) use ($team, $diff) {
                        return $exp->gte($diff, $team->team_category->start_age);
                    });
                }
                if ($team->team_category->end_age) {
                    $query->where(function (QueryExpression $exp, Query $q) use ($team, $diff) {
                        return $exp->lte($diff, $team->team_category->end_age);
                    });
                }
            }
        } else {
            $parameters = $request->getAttribute('parameters');
            // Age is calculated on the end of the year
            $diff = $query->func()->TIMESTAMPDIFF([
                'YEAR' => 'literal',
                'Person.birthdate' => 'identifier',
                \Carbon\Carbon::now()->endOfYear()
            ]);
            if (isset($parameters['filter']['start_age'])) {
                if (preg_match("/([><!]?=?)?\s*([0-9]*)/", $parameters['filter']['start_age'], $matches)) {
                    $operator = $matches[1];
                    if ($operator == null) {
                        $operator = '=';
                    }
                    $method = self::$_logicExpressions[$operator] ?? 'eq';
                    $age = $matches[2];
                    $query->where(function (QueryExpression $exp, Query $q) use ($method, $diff, $age) {
                        return $exp->$method($diff, $age);
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
                    $query->where(function (QueryExpression $exp, Query $q) use ($method, $diff, $age) {
                        return $exp->$method($diff, $age);
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
