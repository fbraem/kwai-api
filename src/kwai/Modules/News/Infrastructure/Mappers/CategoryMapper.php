<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\News\Domain\Category;

/**
 * Class CategoryMapper
 *
 * Mapper for the news Category entity.
 */
class CategoryMapper
{
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Category((object)[
                'name' => $raw->name
            ])
        );
    }
}
