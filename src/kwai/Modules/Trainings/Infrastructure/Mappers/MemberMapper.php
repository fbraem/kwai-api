<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Date;
use Kwai\Core\Domain\ValueObjects\Gender;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Trainings\Domain\Member;

/**
 * Class MemberMapper
 */
class MemberMapper
{
    /**
     * Maps a table record to the Coach domain entity.
     *
     * @param Collection $data
     * @return Member
     */

    public static function toDomain(Collection $data): Member
    {
        return new Member(
            license: $data->get('license'),
            licenseEndDate: Date::createFromString($data->get('license_end_date')),
            name: new Name($data->get('firstname'), $data->get('lastname')),
            gender: new Gender((int) $data->get('gender')),
            birthDate: Date::createFromString($data->get('birthdate')),
        );
    }
}
