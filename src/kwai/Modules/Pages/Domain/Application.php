<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Domain;

use Kwai\Core\Domain\DomainEntity;

/**
 * The Application entity
 *
 * The application associated with the page.
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
     * Get the title of the application.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the name of the application.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
