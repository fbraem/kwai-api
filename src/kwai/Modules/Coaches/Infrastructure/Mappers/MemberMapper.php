<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Coaches\Domain\Member;

/**
 * Class MemberMapper
 */
class MemberMapper
{
    public static function toDomain(Collection $data): Member
    {
        return new Member(
            name: new Name(
                $data->get('firstname'),
                $data->get('lastname')
            )
        );
    }
}
