<?php
/**
 * @package Pages
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Pages\Domain\Author;

/**
 * Class AuthorMapper
 *
 * Mapper for the Author entity
 */
final class AuthorMapper
{
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
