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
    /**
     * Author constructor.
     *
     * @param Name $name
     */
    public function __construct(private Name $name)
    {
    }

    /**
     * Get the username
     */
    public function getName(): Name
    {
        return $this->name;
    }
}
