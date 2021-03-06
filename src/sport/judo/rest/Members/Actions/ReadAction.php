<?php

namespace Judo\REST\Members\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use Judo\Domain\Member\MembersTable;
use Judo\Domain\Member\MemberTransformer;

use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;

class ReadAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');

        $table = MembersTable::getTableFromRegistry();

        $contain = [
            'Person',
            'Person.Contact',
            'Person.Nationality',
            'Person.Contact.Country',
        ];
        if (isset($parameters['include'])) {
            foreach ($parameters['include'] as $include) {
                if ($include == 'trainings') {
                    $contain[] = 'Trainings';
                    $contain[] = 'Trainings.Event';
                }
                if ($include == 'teams') {
                    $contain[] = 'Teams';
                }
            }
        }

        try {
            $member = $table->get($args['id'], [
                'contain' => $contain
            ]);

            $response = (new ResourceResponse(
                MemberTransformer::createForItem(
                    $member
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Member doesn't exist")))($response);
        }
        return $response;
    }
}
