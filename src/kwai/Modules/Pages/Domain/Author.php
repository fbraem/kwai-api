<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\ValueObjects\Name;

/**
 * Class Author
 *
 * The author of the page.
 */
class Author implements DomainEntity
{
    private Name $name;

    /**
     * Author constructor.
     *
     * @param object $props
     */
    public function __construct(object $props)
    {
        $this->name = $props->name;
    }

    /**
     * Get the username
     */
    public function getName(): Name
    {
        return $this->name;
    }
}
