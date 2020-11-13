<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Modules\Users\Domain\ValueObjects\Username;

/**
 * Class Author
 *
 * The author of the page.
 */
class Author implements DomainEntity
{
    private Username $name;

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
    public function getName(): Username
    {
        return $this->name;
    }
}
