<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Modules\Users\Domain\UserRecovery;
use Kwai\Modules\Users\Domain\UserRecoveryEntity;
use Kwai\Modules\Users\Infrastructure\UserRecoveriesTable;
use Kwai\Modules\Users\Infrastructure\UsersTable;

/**
 * Class UserRecoveryDTO
 */
class UserRecoveryDTO
{
    public function __construct(
        public UserRecoveriesTable $userRecoveriesTable = new UserRecoveriesTable(),
        public UsersTable $usersTable = new UsersTable()
    ) {
    }

    public function create(): UserRecovery
    {
        return new UserRecovery(
            uuid: new UniqueId($this->userRecoveriesTable->uuid),
            expiration: new LocalTimestamp(
                Timestamp::createFromString($this->userRecoveriesTable->expired_at),
                $this->userRecoveriesTable->expired_at_timezone
            ),
            user: (new UserDTO($this->usersTable))->createEntity(),
            remark: $this->userRecoveriesTable->remark,
            confirmation: $this->userRecoveriesTable->confirmed_at
                ? Timestamp::createFromString($this->userRecoveriesTable->confirmed_at)
                : null,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->userRecoveriesTable->created_at),
                $this->userRecoveriesTable->updated_at
                    ? Timestamp::createFromString($this->userRecoveriesTable->updated_at)
                    : null
            )
        );
    }

    public function createEntity(): UserRecoveryEntity
    {
        return new UserRecoveryEntity(
            $this->userRecoveriesTable->id,
            $this->create()
        );
    }

    public function persist(UserRecovery $recovery): static
    {
        $this->userRecoveriesTable->remark = $recovery->getRemark();
        $this->userRecoveriesTable->expired_at = (string) $recovery->getExpiration()->getTimestamp();
        $this->userRecoveriesTable->expired_at_timezone = $recovery->getExpiration()->getTimezone();
        $this->userRecoveriesTable->uuid = (string) $recovery->getUuid();
        $this->userRecoveriesTable->confirmed_at = $recovery->isConfirmed()
            ? (string) $recovery->getConfirmation()
            : null
        ;
        $this->userRecoveriesTable->created_at = strval($recovery->getTraceableTime()->getCreatedAt());
        $this->userRecoveriesTable->updated_at = $recovery->getTraceableTime()->getUpdatedAt()?->__toString();
        $this->userRecoveriesTable->user_id = $recovery->getUser()->id();
        return $this;
    }

    public function persistEntity(UserRecoveryEntity $recovery): static
    {
        $this->userRecoveriesTable->id = $recovery->id();
        return $this->persist($recovery->domain());
    }
}