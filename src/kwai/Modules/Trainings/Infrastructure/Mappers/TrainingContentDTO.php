<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Modules\Trainings\Infrastructure\TrainingContentsTable;
use Kwai\Modules\Trainings\Infrastructure\UsersTable;

/**
 * Class ContentDTO
 */
class TrainingContentDTO
{
    public function __construct(
        public TrainingContentsTable $content,
        public UsersTable $user
    ) {
    }
}
