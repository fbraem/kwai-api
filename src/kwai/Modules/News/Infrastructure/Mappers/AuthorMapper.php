<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
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
     * @param object $raw
     * @return Entity<Author>
     */
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Author((object)[
                'name' => new Name($raw->first_name, $raw->last_name)
            ])
        );
    }
}
