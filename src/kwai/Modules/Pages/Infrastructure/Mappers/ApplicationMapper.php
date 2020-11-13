<?php
/**
 * @package Pages
 * @subpackage Infrastucture
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Pages\Domain\Application;

/**
 * Class ApplicationMapper
 *
 * Mapper for the page Application entity.
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
