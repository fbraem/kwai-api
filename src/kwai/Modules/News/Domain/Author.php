<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Domain;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Modules\Users\Domain\ValueObjects\Username;

/**
 * Class Author
 *
 * The author of the text
 */
class Author implements DomainEntity
{
    /**
     * The name of the author.
     */
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
}
