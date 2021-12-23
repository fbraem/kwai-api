<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;

/**
 * Class AuthorMapper
 *
 * Mapper for the Author entity
 */
final class AuthorMapper
{
    public static function toDomain(Collection $data): Creator
    {
        return new Creator(
            (int) $data->get('id'),
            name: new Name(
                $data->get(
                    'first_name',
                    $data->get('last_name')
                )
            )
        );
    }
}
