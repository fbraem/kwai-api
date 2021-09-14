<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Presentation\REST;

use Kwai\Core\Infrastructure\Presentation\InputSchema;
use Kwai\Modules\Coaches\UseCases\UpdateCoachCommand;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;

/**
 * Class CoachSchema
 */
class CoachSchema implements InputSchema
{
    /**
     * Constructor.
     *
     * @param bool $create Is this used for creating a new coach?
     */
    public function __construct(
        private bool $create = false
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(): Structure
    {
        return Expect::structure([
            'data' => Expect::structure([
                'type' => Expect::anyOf('coaches'),
                'id' => Expect::string()->required(!$this->create),
                'attributes' => Expect::structure([
                    'active' => Expect::bool(false),
                    'diploma' => Expect::string(),
                    'bio' => Expect::string(),
                    'remark' => Expect::string()->nullable()
                ])
            ])
        ]);
    }

    /**
     * @inheritDoc
     */
    public function process($normalized): UpdateCoachCommand
    {
        $command = new UpdateCoachCommand();
        $command->id = (int) $normalized->data->id;

        $command->active = $normalized->data->attributes->active;
        $command->diploma = $normalized->data->attributes->diploma;
        $command->bio = $normalized->data->attributes->bio;
        $command->remark = $normalized->data->attributes->remark ?? null;

        return $command;
    }
}
