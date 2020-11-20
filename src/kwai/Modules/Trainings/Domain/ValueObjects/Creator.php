<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Domain\ValueObjects;

use Kwai\Core\Domain\ValueObjects\Name;

/**
 * Class Creator
 *
 * Value object for a creator.
 */
class Creator
{
    private int $id;

    private Name $name;

    /**
     * Creator constructor.
     *
     * @param int    $id
     * @param Name $name
     */
    public function __construct(int $id, Name $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Returns the id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns the name
     */
    public function getName(): Name
    {
        return $this->name;
    }
}
