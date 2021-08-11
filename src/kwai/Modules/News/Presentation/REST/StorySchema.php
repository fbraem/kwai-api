<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\REST;

use Kwai\Core\Infrastructure\Presentation\InputSchema;
use Kwai\Core\Infrastructure\Presentation\TextInputSchema;
use Kwai\Modules\News\UseCases\CreateStoryCommand;
use Kwai\Modules\News\UseCases\UpdateStoryCommand;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;

/**
 * Class StorySchema
 */
class StorySchema implements InputSchema
{
    private const DATETIME_PATTERN = '\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}';

    private TextInputSchema $textSchema;

    /**
     * Constructor.
     *
     * @param bool $create Is this used for creating a new Story?
     */
    public function __construct(
        private bool $create = false
    ) {
        $this->textSchema = new TextInputSchema();
    }

    public function create(): Structure
    {
        return Expect::structure([
            'data' => Expect::structure([
                'type' => Expect::anyOf('stories'),
                'id' => Expect::string()->required(!$this->create),
                'attributes' => Expect::Structure([
                    'enabled' => Expect::bool(false),
                    'publish_date' => Expect::string()->required()->pattern(self::DATETIME_PATTERN),
                    'timezone' => Expect::string()->required(),
                    'end_date' => Expect::string()->nullable()->pattern(self::DATETIME_PATTERN),
                    'promotion' => Expect::int(0),
                    'promotion_end_date' => Expect::string()->nullable()->pattern(self::DATETIME_PATTERN),
                    'remark' => Expect::string()->nullable(),
                    'contents' => Expect::arrayOf(
                        $this->textSchema->create()
                    )->required()
                ]),
                'relationships' => Expect::structure([
                    'application' => Expect::structure([
                        'data' => Expect::structure([
                            'type' => Expect::string()->required(),
                            'id' => Expect::string()->required()
                        ])
                    ])
                ])
            ])
        ]);
    }

    public function process($normalized): CreateStoryCommand | UpdateStoryCommand
    {
        if ($this->create) {
            $command = new CreateStoryCommand();
        } else {
            $command = new UpdateStoryCommand();
            $command->id = (int) $normalized->data->id;
        }

        $command->enabled = $normalized->data->attributes->enabled;
        $command->application = (int) $normalized->data->relationships->application->data->id;
        $command->promotion = $normalized->data->attributes->promotion;
        $command->promotion_end_date = $normalized->data->attributes->promotion_end_date ?? null;
        $command->end_date = $normalized->data->attributes->end_date ?? null;
        $command->publish_date = $normalized->data->attributes->publish_date;
        $command->timezone = $normalized->data->attributes->timezone;
        $command->remark = $normalized->data->attributes->remark ?? null;
        $command->contents = $normalized->data->attributes->contents;

        return $command;
    }
}
