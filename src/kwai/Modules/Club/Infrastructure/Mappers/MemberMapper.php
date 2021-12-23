<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Club\Domain\Member;

/**
 * Class MemberMapper
 */
class MemberMapper
{
    /**
     * Maps a data transfer object to a Member entity.
     *
     * @param MemberDTO $dto
     * @return Member
     */
    public static function toDomain(MemberDTO $dto): Member
    {
        return new Member(
            name: new Name(
                $dto->personData->firstname,
                $dto->personData->lastname
            )
        );
    }
}
