<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Modules\News\Domain\Application;

/**
 * Class ApplicationMapper
 *
 * Mapper for the news Application entity.
 */
class ApplicationMapper
{
    public static function toDomain(Collection $data): Application
    {
        return new Application(
            title: $data->get('title'),
            name: $data->get('name')
        );
    }
}
