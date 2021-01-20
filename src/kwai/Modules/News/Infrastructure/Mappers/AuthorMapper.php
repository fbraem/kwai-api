<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\News\Domain\Author;

/**
 * Class AuthorMapper
 *
 * Mapper for the Author entity
 */
final class AuthorMapper
{
    /**
     * @param Collection $data
     * @return Author
     */
    public static function toDomain(Collection $data): Author
    {
        return new Author(
            name: new Name(
                $data->get('first_name'),
                $data->get('last_name')
            )
        );
    }
}
