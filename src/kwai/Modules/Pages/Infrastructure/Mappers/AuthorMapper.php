<?php
/**
 * @package Pages
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Pages\Domain\Author;
use Kwai\Modules\Users\Domain\ValueObjects\Username;

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
                'name' => new Username($raw->first_name ?? null, $raw->last_name ?? null)
            ])
        );
    }
}
