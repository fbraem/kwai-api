<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Applications\Author\Actions;

use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Modules\News\UseCases\Content;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;

/**
 * Class SaveStoryAction
 *
 * Abstract class for creating/updating a story.
 */
abstract class SaveStoryAction extends Action
{
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
                'attributes' => Expect::Structure([
                    'enabled' => Expect::bool(false),
                    'publish_date' => Expect::string()->required(),
                    'timezone' => Expect::string()->required(),
                    'end_date' => Expect::string(),
                    'promoted' => Expect::int(0),
                    'promotion_end_date' => Expect::string(),
                    'remark' => Expect::string(),
                    'contents' => Expect::arrayOf(Expect::structure([
                        'title' => Expect::string()->required(),
                        'locale' => Expect::string('nl'),
                        'format' => Expect::string('md'),
                        'summary' => Expect::string()->required(),
                        'content' => Expect::string()
                    ]))->required()
                ]),
                'relationships' => Expect::structure([
                    'category' => Expect::structure([
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
        $command->category = (int) $normalized->data->relationships->category->data->id;
        $command->promoted = $normalized->data->attributes->promoted;
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
