<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\ValueObjects\Date;
use Kwai\Core\Domain\ValueObjects\Gender;
use Kwai\Core\Domain\ValueObjects\Name;

/**
 * Class Member
 *
 * Entity that represents a member of the club.
 */
class Member implements DomainEntity
{
    private string $license;

    private Date $licenseEndDate;

    private Name $name;

    private Gender $gender;

    private Date $birthDate;

    /**
     * Member constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->license = $props->license;
        $this->licenseEndDate = $props->licenseEndDate;
        $this->name = $props->name;
        $this->gender = $props->gender;
        $this->birthDate = $props->birthDate;
    }

    /**
     * @return string
     */
    public function getLicense(): string
    {
        return $this->license;
    }

    /**
     * @return Date
     */
    public function getLicenseEndDate(): Date
    {
        return $this->licenseEndDate;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return Gender
     */
    public function getGender(): Gender
    {
        return $this->gender;
    }

    /**
     * @return Date
     */
    public function getBirthDate(): Date
    {
        return $this->birthDate;
    }
}
