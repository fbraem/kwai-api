<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;

/**
 * Class CreatorMapper
 */
class CreatorMapper
{
    /**
     * Maps a record to Creator value object
     *
     * @param Collection $data
     * @return Creator
     */
    public static function toDomain(Collection $data): Creator
    {
        return new Creator(
            id: (int) $data->get('id'),
            name: new Name(
                $data->get('first_name'),
                $data->get('last_name')
            )
        );
    }
}