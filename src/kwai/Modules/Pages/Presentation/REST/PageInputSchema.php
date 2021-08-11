<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Presentation\REST;

use Kwai\Core\Infrastructure\Presentation\InputSchema;
use Kwai\Core\Infrastructure\Presentation\TextInputSchema;
use Kwai\Modules\Pages\UseCases\CreatePageCommand;
use Kwai\Modules\Pages\UseCases\UpdatePageCommand;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;

/**
 * Class PageInputSchema
 */
class PageInputSchema implements InputSchema
{
    private TextInputSchema $textSchema;

    public function __construct(
        private bool $create = false
    ) {
        $this->textSchema = new TextInputSchema();
    }

    /**
     * @inheritDoc
     */
    public function create(): Structure
    {
        return Expect::structure([
            'data' => Expect::structure([
                'type' => Expect::string(),
                'id' => Expect::string()->required(!$this->create),
                'attributes' => Expect::Structure([
                    'enabled' => Expect::bool(false),
                    'priority' => Expect::int(0),
                    'remark' => Expect::string()->nullable(),
                    'contents' => Expect::arrayOf(
                        $this->textSchema->create()
                    )->required()
                ]),
                'relationships' => Expect::structure([
                    'application' => Expect::structure([
                        'data' => Expect::structure([
                            'type' => Expect::string(),
                            'id' => Expect::string()
                        ])
                    ])->required()
                ])
            ])
        ]);
    }

    /**
     * @inheritDoc
     */
    public function process($normalized)
    {
        $command = $this->create ? new CreatePageCommand() : new UpdatePageCommand();
        $command->enabled = $normalized->data->attributes->enabled;
        $command->application = (int) $normalized->data->relationships->application->data->id;
        $command->priority = $normalized->data->attributes->priority;
        $command->remark = $normalized->data->attributes->remark ?? null;
        foreach ($normalized->data->attributes->contents as $content) {
            $command->contents[] = $this->textSchema->process($content);
        }
        return $command;
    }
}
