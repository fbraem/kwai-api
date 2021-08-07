<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\REST;

use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\UseCases\Content;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Psr\Log\LoggerInterface;

/**
 * Class SaveStoryAction
 *
 * Abstract class for creating/updating a story.
 */
abstract class SaveStoryAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create the schema used to normalize and validate the structure of
     * the input body.
     *
     * @return Structure
     */
    protected function createSchema(): Structure
    {
        return Expect::structure([
            'data' => Expect::structure([
                'type' => Expect::string(),
                'id' => Expect::string(null),
                'attributes' => Expect::Structure([
                    'enabled' => Expect::bool(false),
                    'publish_date' => Expect::string()->required(),
                    'timezone' => Expect::string()->required(),
                    'end_date' => Expect::string()->nullable(),
                    'promotion' => Expect::int(0),
                    'promotion_end_date' => Expect::string()->nullable(),
                    'remark' => Expect::string()->nullable(),
                    'contents' => Expect::arrayOf(Expect::structure([
                        'title' => Expect::string()->required(),
                        'locale' => Expect::string('nl'),
                        'format' => Expect::string('md'),
                        'summary' => Expect::string()->required(),
                        'content' => Expect::string()
                    ]))->required()
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
     * Return the command for the use case.
     *
     * @return mixed
     */
    abstract protected function createCommand();

    /**
     * Process the input body.
     *
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    protected function processInput($data)
    {
        $processor = new Processor();
        $normalized = $processor->process($this->createSchema(), $data);
        $command = $this->createCommand();

        $command->enabled = $normalized->data->attributes->enabled;
        $command->application = (int) $normalized->data->relationships->application->data->id;
        $command->promotion = $normalized->data->attributes->promotion;
        $command->promotion_end_date = $normalized->data->attributes->promotion_end_date ?? null;
        $command->end_date = $normalized->data->attributes->end_date ?? null;
        $command->publish_date = $normalized->data->attributes->publish_date;
        $command->timezone = $normalized->data->attributes->timezone;
        $command->remark = $normalized->data->attributes->remark ?? null;
        foreach ($normalized->data->attributes->contents as $content) {
            $c = new Content();
            $c->content = $content->content;
            $c->format = $content->format;
            $c->locale = $content->locale;
            $c->summary = $content->summary;
            $c->title = $content->title;
            $command->contents[] = $c;
        }

        return $command;
    }
}
