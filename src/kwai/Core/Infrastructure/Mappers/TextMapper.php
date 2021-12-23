<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Text;

/**
 * Class TextMapper
 */
class TextMapper
{
    /**
     * Maps a record to a Text value object.
     *
     * @param Collection $data
     * @return Text
     */
    public static function toDomain(Collection $data): Text
    {
        return new Text(
            locale: new Locale($data->get('locale')),
            format: new DocumentFormat($data->get('format')),
            title: $data->get('title'),
            summary: $data->get('summary'),
            content: $data->get('content'),
            author: CreatorMapper::toDomain($data->get('creator'))
        );
    }

    public static function toPersistence(Text $text): Collection
    {
        return collect([
            'locale' => (string) $text->getLocale(),
            'format' => (string) $text->getFormat(),
            'title' => $text->getTitle(),
            'summary' => $text->getSummary(),
            'content' => $text->getContent(),
            'user_id' => $text->getAuthor()->getId()
        ]);
    }
}
