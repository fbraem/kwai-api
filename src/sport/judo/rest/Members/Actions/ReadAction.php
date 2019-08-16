<?php

namespace Judo\REST\Members\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use Judo\Domain\Member\MembersTable;
use Judo\Domain\Member\MemberTransformer;

use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

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

        $contain = [ 'Person' ];
        if (isset($parameters['include'])) {
            foreach ($parameters['include'] as $include) {
                if ($include == 'trainings') {
                    $contain[] = 'Trainings';
                    $contain[] = 'Trainings.Event';
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
