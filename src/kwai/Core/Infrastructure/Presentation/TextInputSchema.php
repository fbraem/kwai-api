<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation;

use Kwai\Core\UseCases\Content;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;

/**
 * Class TextInputSchema
 *
 * InputSchema for the Text value object
 */
class TextInputSchema implements InputSchema
{
    /**
     * @inheritDoc
     */
    public function create(): Structure
    {
        return Expect::structure([
            'title' => Expect::string()->required(),
            'locale' => Expect::string('nl'),
            'format' => Expect::string('md'),
            'summary' => Expect::string()->required(),
            'content' => Expect::string()->nullable()
        ]);
    }

    /**
     * @inheritDoc
     * @return Content
     */
    public function process($normalized)
    {
        $c = new Content();
        $c->content = $normalized->content;
        $c->format = $normalized->format;
        $c->locale = $normalized->locale;
        $c->summary = $normalized->summary;
        $c->title = $normalized->title;
        return $c;
    }
}
