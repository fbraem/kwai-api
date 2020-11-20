<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
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
     * @param object $raw
     * @return Entity<Member>
     */

    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Member((object) [
                'license' => $raw->license,
                'licenseEndDate' => Date::createFromString($raw->license_end_date),
                'name' => new Name($raw->firstname, $raw->lastname),
                'gender' => new Gender((int) $raw->gender),
                'birthDate' => Date::createFromString($raw->birthdate),
            ])
        );
    }
}
