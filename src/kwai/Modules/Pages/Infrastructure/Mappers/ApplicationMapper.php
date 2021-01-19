<?php
/**
 * @package Pages
 * @subpackage Infrastucture
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Modules\Pages\Domain\Application;

/**
 * Class ApplicationMapper
 *
 * Mapper for the page Application entity.
 */
class ApplicationMapper
{
    /**
     * Maps persistence to domain
     *
     * @param Collection $data
     * @return Application
     */
    public static function toDomain(Collection $data): Application
    {
        return new Application(
            title: $data->get('title'),
            name: $data->get('name')
        );
    }
}
