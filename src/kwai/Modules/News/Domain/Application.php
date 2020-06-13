<?php
/**
 * @package
 * @subpackage
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
     * The title of the application
     */
    private string $title;

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
     * Get the name of the application
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}