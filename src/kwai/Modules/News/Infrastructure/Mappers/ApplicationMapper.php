<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\News\Domain\Application;

/**
 * Class ApplicationMapper
 *
 * Mapper for the news Application entity.
 */
class ApplicationMapper
{
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Application((object)[
                'title' => $raw->title,
                'name' => $raw->name
            ])
        );
    }
}
