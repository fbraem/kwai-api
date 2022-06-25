<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Name;
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

    public function create(): Text
    {
        return new Text(
            locale: Locale::from($this->content->locale),
            format: DocumentFormat::from($this->content->format),
            title: $this->content->title,
            author: new Creator(
                id: $this->user->id,
                name: new Name(
                    $this->user->first_name,
                    $this->user->last_name
                )
            ),
            summary: $this->content->summary,
            content: $this->content->content
        );
    }
}
