<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Resources;

use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\UserRecoveryEntity;

/**
 * Class UserRecoveryResource
 */
#[JSONAPI\Resource(type: ResourceTypes::USER_RECOVERIES, id: 'getId')]
class UserRecoveryResource
{
    public function __construct(
        private readonly UserRecoveryEntity $userRecovery
    ) {
    }

    public function getId(): string
    {
        return (string) $this->userRecovery->getUuid();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreationDate(): string
    {
        return (string) $this->userRecovery->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdateDate(): ?string
    {
        return $this->userRecovery->getTraceableTime()->getUpdatedAt()?->__toString();
    }
}