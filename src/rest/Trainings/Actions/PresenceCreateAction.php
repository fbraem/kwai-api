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

            $training = $table->get($args['id'], [
                'contain' => [
                    'Members',
                    'Members.Person',
                ]
            ]);

            $members = [];
            foreach ($data['data'] as $item) {
                $members[] = $table->Members->get($item['id']);
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
                $toInsert = [];
                foreach ($members as $member) {
                    if (!$lookup[$member->id]) {
                        $member->_joinData = new Entity([
                            'remark' => '',
                            'user' => $request->getAttribute('clubman.user')
                        ], [
                            'markNew' => true
                        ]);
                        $toInsert[] = $member;
                    }
                }
                if (count($toInsert) > 0) {
                    $table->Members->link($training, $toInsert);
                }
            }
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Training doesn't exist")))($response);
        }

        return $response;
    }
}
