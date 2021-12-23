<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Domain;

use Kwai\Core\Domain\DomainEntity;

/**
 * Class Application
 *
 * Application associated with a news story
 */
class Application implements DomainEntity
{
    /**
     * Application constructor.
     *
     * @param string $title
     * @param string $name
     */
    public function __construct(
        private string $title,
        private string $name
    ) {
    }

    /**
     * Get the title of the application
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the name of the application
     */
    public function getName(): string
    {
        return $this->name;
    }


}
