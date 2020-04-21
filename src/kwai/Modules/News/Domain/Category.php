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
class Category implements DomainEntity
{
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
}
