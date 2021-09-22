<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Infrastructure\Mappers;

use Kwai\Modules\Club\Infrastructure\ContactsTableSchema;
use Kwai\Modules\Club\Infrastructure\MembersTableSchema;
use Kwai\Modules\Club\Infrastructure\PersonsTableSchema;

/**
 * Class MemberDTO
 */
class MemberDTO
{
    public function __construct(
        public MembersTableSchema $memberData,
        public ContactsTableSchema $contactData,
        public PersonsTableSchema $personData
    ) {
    }
}
