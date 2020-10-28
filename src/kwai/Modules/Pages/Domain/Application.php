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
     * The title of the application.
     */
    private string $title;

    /**
     * The name of the application
     */
    private string $name;

    /**
     * Application constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->title = $props->title;
        $this->name = $props->name;
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
