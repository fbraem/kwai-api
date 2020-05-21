<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Domain;

use Kwai\Core\Domain\DomainEntity;

/**
 * Class Category
 *
 * A news category
 */
class Application implements DomainEntity
{
    /**
     * The name of the category
     */
    private string $name;

    /**
     * Category constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->name = $props->name;
    }

    /**
     * Get the name of the category
     */
    public function getName(): string
    {
        return $this->name;
    }
}
