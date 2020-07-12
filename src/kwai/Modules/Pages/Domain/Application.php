<?php
/**
 * @package Pages
 * @subpackage Domain
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
    private String $title;

    /**
     * Application constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->title = $props->title;
    }

    /**
     * Get the title of the application.
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
