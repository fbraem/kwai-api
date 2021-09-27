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
    //TODO: rename description column in bio?

    /**
     * Maps a table record to the Coach domain entity.
     *
     * @param Collection $data
     * @return Coach
     */
    public static function toDomain(Collection $data): Coach
    {
        if ($data->has('user')) {
            $user = new Entity(
                (int) $data->get('user')->get('id'),
                UserMapper::toDomain($data->get('user'))
            );
        } else {
            $user = null;
        }
        return new Coach(
            member: new Entity(
                (int) $data->get('member')->get('id'),
                MemberMapper::toDomain($data->get('member'))
            ),
            user: $user,
            bio: $data->get('description', null),
            diploma: $data->get('diploma', null),
            active: $data->get('active') === '1',
            remark: $data->get('remark', null)
        );
    }

    public static function toPersistence(Coach $coach): Collection
    {
        return collect([
            'active' => $coach->isActive(),
            'member_id' => $coach->getMember()->id(),
            'description' => $coach->getBio(),
            'diploma' => $coach->getDiploma(),
            'remark' => $coach->getRemark(),
            'created_at'=> $coach->getTraceableTime()->getCreatedAt(),
            'updated_at'=> $coach->getTraceableTime()->getUpdatedAt()?->__toString(),
            'user_id' => $coach->getUser()?->id()
        ]);
    }
}
