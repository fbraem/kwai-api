<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Mappers\CreatorMapper;
use Kwai\Modules\Coaches\Domain\Coach;

/**
 * Class CoachMapper
 */
class CoachMapper
{
    /**
     * Maps a table record to the Coach domain entity.
     *
     * @param Collection $data
     * @return Coach
     */
    public static function toDomain(Collection $data): Coach
    {
        return new Coach(
            member: new Entity(
                (int) $data->get('member')->get('id'),
                MemberMapper::toDomain($data->get('member'))
            ),
            creator: CreatorMapper::toDomain($data->get('creator')),
            bio: $data->get('description') ?? '',
            diploma: $data->get('diploma', ''),
            active: $data->get('active') === '1',
            remark: $data->get('remark', null)
        );
    }
}
