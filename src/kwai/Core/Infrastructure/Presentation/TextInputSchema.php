<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Presentation;

use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\UseCases\Content;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Nette\Schema\ValidationException;
use ValueError;

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
    public function process($normalized): Content
    {
        $c = new Content();
        $c->content = $normalized->content;
        try {
            $c->format = DocumentFormat::from($normalized->format);
        } catch (ValueError) {
            throw new ValidationException("Invalid document format {$normalized->format}");
        }
        try {
            $c->locale = Locale::from($normalized->locale);
        } catch (ValueError) {
            throw new ValidationException("Invalid locale {$normalized->locale}");
        }
        $c->summary = $normalized->summary;
        $c->title = $normalized->title;
        return $c;
    }
}
