<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Trainings\Infrastructure\UsersTable;

/**
 * Class CreatorDTO
 */
final class CreatorDTO
{
    public function __construct(
        public UsersTable $user = new UsersTable()
    ) {
    }

    public function create(): Creator
    {
        return new Creator(
            $this->user->id,
            new Name(
                $this->user->first_name,
                $this->user->last_name
            )
        );
    }
}
