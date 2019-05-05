<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use \Cake\ORM\Entity;

use Domain\Training\TrainingsTable;
use Domain\Training\TrainingTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class PresenceCreateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        try {
            $table = TrainingsTable::getTableFromRegistry();

            $training = $table->get(
                $args['id'],
                [
                    'contain' => [
                        'Members',
                        'Members.Person',
                    ]
                ]
            );

            $members = [];
            $remarks = [];
            try {
                foreach ($data['data'] as $item) {
                    $members[] = $table->Members->get(
                        $item['id'],
                        [
                            'contain' => [
                                'Person'
                            ]
                        ]
                    );
                    $remarks[$item['id']] = $item['attributes']['remark'];
                }
            } catch (RecordNotFoundException $rnfe) {
                $response = (new NotFoundResponse(_("Member doesn't exist")))($response);
                return;
            }

            // Update members
            if ($members) {
                // When a member is not passed to this function, it must be deleted
                $lookup = array_column($members, null, 'id');
                $toDelete = [];
                foreach ($training->presences as $member) {
                    if (!$lookup[$member->id]) {
                        $toDelete[] = $member;
                    }
                }
                if (count($toDelete) > 0) {
                    $table->Members->unlink($training, $toDelete);
                }

                // When a member is passed to this function and it's not in the
                // table, it must be insert
                $lookup = array_column($training->presences, null, 'id');
                $presences = [];
                foreach ($members as $member) {
                    $presence = $lookup[$member->id];
                    if ($presence) {
                        if ($remarks[$member->id]) {
                            $presence->_joinData['remark'] = $remarks[$member->id];
                        }
                        $presences[] = $presence;
                    } else {
                        $member->_joinData = new Entity([
                            'remark' => $remarks[$member->id],
                            'user' => $request->getAttribute('clubman.user')
                        ], [
                            'markNew' => true
                        ]);
                        if ($remarks[$member->id]) {
                            $member->_joinData['remark'] = $remarks[$member->id];
                        }
                        $presences[] = $member;
                    }
                }
                if (count($presences) > 0) {
                    $table->Members->link($training, $presences);
                }
            }

            $response = (new ResourceResponse(
                TrainingTransformer::createForItem(
                    $training
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Training doesn't exist")))($response);
        }

        return $response;
    }
}
